<?php

declare(strict_types=1);

namespace proxy;


use pocketmine\{
	network\mcpe\protocol\PacketPool, utils\Terminal
};
use proxy\{
	plugin\Plugin, utils\Address, utils\Log, utils\Packet
};
use proxy\hosts\{
	BaseHost, Client, Server
};
use raklib\protocol\{
	ACK, OpenConnectionRequest1, UnconnectedPing, UnconnectedPong
};

class Proxy{
	/** @var resource $socket */
	private $socket;
	/** @var Server $server */
	private $server;
	/** @var Client $client */
	private $client;
	/** @var Plugin[] $plugins */
	private $plugins = [];

	/**
	 * Proxy constructor.
	 * @param string $serverAddress
	 * @param int    $serverPort
	 * @param string $interface
	 * @param int    $bindPort
	 * @param bool   $withoutPlugins
	 */
	public function __construct(string $serverAddress, int $serverPort = 19132, string $interface = "0.0.0.0", int $bindPort = 19132, $withoutPlugins = false){
		Terminal::init();
		PacketPool::init();
		Packet::init();

		if($serverPort !== $bindPort){
			Log::Warn("The proxy port is different from the server port. If there is a port check on the server, then you can not join it.");
		}

		$this->server = new Server($this, new Address($serverAddress, $serverPort));
		$this->client = new Client($this, null);

		$this->socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
		if(!socket_bind($this->socket, $interface, $bindPort)){
			Log::Error("Failed to bind to $interface:$bindPort");
			exit(1);
		}

		socket_set_option($this->socket, SOL_SOCKET, SO_SNDBUF, 1024 * 1024 * 8);
		socket_set_option($this->socket, SOL_SOCKET, SO_RCVBUF, 1024 * 1024 * 8);

		$withoutPlugins ? Log::Warn("Plugins have been disabled.") : $this->enablePlugins();

		while(true){
			if(@socket_recvfrom($this->socket, $buffer, 65535, 0, $address, $port) !== false){
				$internetAddress = new Address($address, $port);
				if(!$this->client->connected){
					switch(ord($buffer{0})){
						case UnconnectedPing::$ID:
							$this->client->address = $internetAddress;
							$this->sendToServer($buffer);
							break;
						case UnconnectedPong::$ID:
							$this->server->data = explode(";", substr($buffer, 40));
							$this->sendToClient($buffer);
							break;
						case OpenConnectionRequest1::$ID:
							$this->client->address = $internetAddress;
							$this->client->connected = true;
							$this->sendToServer($buffer);
							break;
					}
				}else{
					if($this->server->address->equals($internetAddress)){
						if($this->decodeBuffer($buffer, $this->server)){
							$this->sendToClient($buffer);
						}
					}else{
						if($this->decodeBuffer($buffer, $this->client)){
							$this->sendToServer($buffer);
						}
					}
				}
			}
		}
	}

	/**
	 * @param string   $buffer
	 * @param BaseHost $host
	 * @return bool
	 */
	private function decodeBuffer(string $buffer, BaseHost $host) : bool{
		if(($packet = Packet::readDataPacket($buffer)) !== null){
			$host->handleDataPacket($packet);
			if(!empty($this->plugins)){
				foreach($this->plugins as $plugin){
					if(!($host instanceof Client ? $plugin->handleClientDataPacket($packet) : $plugin->handleServerDataPacket($packet))){
						$ack = new ACK;
						$ack->packets[] = Packet::$storage[$packet];
						$ack->encode();
						$host->writePacket($ack->buffer);
						return false;
					}
				}
			}
		}
		return true;
	}

	/**
	 * @param string $buffer
	 */
	public function sendToServer(string $buffer) : void{
		$this->server->writePacket($buffer);
	}

	/**
	 * @param string $buffer
	 */
	public function sendToClient(string $buffer) : void{
		$this->client->writePacket($buffer);
	}

	/**
	 * @param string $buffer
	 * @param string $host
	 * @param int    $port
	 */
	public function writePacket(string $buffer, string $host, int $port) : void{
		socket_sendto($this->socket, $buffer, strlen($buffer), 0, $host, $port);
	}

	/**
	 * @return Server
	 */
	public function getServer() : Server{
		return $this->server;
	}

	/**
	 * @return Client
	 */
	public function getClient() : Client{
		return $this->client;
	}

	public function enablePlugins() : void{
		foreach(glob("plugins/*") as $file){
			if(is_dir($file) && file_exists($yml_file = $file . "/" . "plugin.yml")){
				$yml = yaml_parse_file($yml_file);
				if(isset($yml["name"]) && isset($yml["main"])){
					Log::Info("Loading plugin \"" . $yml["name"] . "\"...");
					if(file_exists($main = dirname($yml_file) . "/src/" . str_replace("\\", "/", $yml["main"]) . ".php")){
						/** @noinspection PhpIncludeInspection */
						require_once $main;
						if(class_exists($yml["main"])){
							$plugin = new $yml["main"]($this);
							if($plugin instanceof Plugin){
								$this->plugins[] = $plugin;
								$plugin->onEnable();
							}
						}
					}
				}
			}
		}
	}
}