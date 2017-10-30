<?php

declare(strict_types=1);

namespace proxy;


use pocketmine\network\mcpe\protocol\BatchPacket;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\PacketPool;
use pocketmine\network\mcpe\protocol\UpdateAttributesPacket;
use pocketmine\utils\TextFormat;
use raklib\protocol\DATA_PACKET_4;
use raklib\protocol\EncapsulatedPacket;
use raklib\protocol\OpenConnectionRequest1;
use raklib\protocol\UnconnectedPing;
use raklib\protocol\UnconnectedPong;
use raklib\RakLib;

class Proxy
{
    /** @var resource */
    private $socket;
    /** @var  string */
    protected $serverHost;
    /** @var  int */
    protected $serverPort;
    /** @var  string */
    protected $clientHost;
    /** @var  int */
    protected $clientPort;

    /**
     * Proxy constructor.
     * @param string $serverHost
     * @param int    $serverPort
     */
    public function __construct(string $serverHost, int $serverPort = 19132)
    {
        $this->socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        if (@socket_bind($this->socket, "0.0.0.0", 19132) === true) {
            socket_set_option($this->socket, SOL_SOCKET, SO_SNDBUF, 1024 * 1024 * 8);
            socket_set_option($this->socket, SOL_SOCKET, SO_RCVBUF, 1024 * 1024 * 8);
        } else {
            echo("**** FAILED TO BIND TO " . "0.0.0.0" . ":" . 19132 . "!");
            echo("Perhaps a server is already running on that port?");
            exit(1);
        }
        socket_set_nonblock($this->socket);

        PacketPool::init();
        $this->serverHost = gethostbyname($serverHost);
        $this->serverPort = $serverPort;
        socket_set_option($this->socket, SOL_SOCKET, SO_SNDBUF, 1024 * 1024 * 8);
        socket_set_option($this->socket, SOL_SOCKET, SO_RCVBUF, 1024 * 1024 * 8);

        echo "
        \e[38;5;87m _____                     
        \e[38;5;87m|  __ \                    
        \e[38;5;87m| |__) | __ _____  ___   _ 
        \e[38;5;87m|  ___/ '__/ _ \ \/ / | | |
        \e[38;5;87m| |   | | | (_) >  <| |_| |
        \e[38;5;87m|_|   |_|  \___/_/\_\ __, |
        \e[38;5;87m                      __/ |
        \e[38;5;87m                     |___/ 
		\e[m" . PHP_EOL;

        echo "\e[38;5;227mWaiting for ping from the client...\e[m" . PHP_EOL;
        while (true) {
            $len = socket_recvfrom($this->socket, $buffer, 65535, 0, $this->clientHost, $this->clientPort);
            if ($len !== false && ord($buffer[0]) == UnconnectedPing::$ID) {
                echo "\e[38;5;83mReceived a ping from the client!\e[m" . PHP_EOL;
                break;
            }
        }

        echo "\e[38;5;145mSending ping to " . $this->serverHost . "...\e[m" . PHP_EOL;
        $ping = "\x01" . pack("NN", mt_rand(0, 0x7fffffff), mt_rand(0, 0x7fffffff)) . RakLib::MAGIC;
        socket_sendto($this->socket, $ping, strlen($ping), 0, $this->serverHost, $this->serverPort);

        echo "\e[38;5;227mWaiting for a response from the server...\e[m" . PHP_EOL;
        $pong = "";
        while (true) {
            socket_recvfrom($this->socket, $buffer, 65535, 0, $h, $p);
            if ($h === $this->serverHost and $this->serverPort === $h and ord($pong{0}) === UnconnectedPong::$ID) {
                echo "\e[38;5;83mReceived response from server!\e[m" . PHP_EOL;
                $info = explode(";", substr($pong, 35));
                echo "\e[38;5;87m\tMOTD: " . TextFormat::toANSI($info[1]) . PHP_EOL;
                echo "\tVersion: " . $info[3] . ", Protocol: " . $info[2] . PHP_EOL;
                echo "\tPlayers: " . $info[4] . "/" . $info[5] . "\e[m" . PHP_EOL;
                break;
            }
        }
        socket_sendto($this->socket, $pong, strlen($pong), 0, $this->clientHost, $this->clientPort);

        echo "\e[38;5;227mWaiting for login to the server\e[m" . PHP_EOL;
        while (true) {
            $len = socket_recvfrom($this->socket, $buffer, 65535, 0, $this->clientHost, $this->clientPort);
            if ($len !== false && ord($buffer{0}) === OpenConnectionRequest1::$ID) {
                echo "\e[38;5;83mReceived OpenConnectionRequest1 from client\e[m" . PHP_EOL;
                break;
            }
        }

        $this->Listen();
    }

    /**
     * Listens and sends packets
     */
    protected function Listen(): void
    {
        while (true) {
            $status = @socket_recvfrom($this->socket, $buffer, 65535, 0, $source, $port);
            if ($status !== false) {
                if ($source === $this->serverHost and $port === $this->serverPort) {
                    if (($pk = $this->readDataPacket($buffer)) !== null) {
                        echo "\e[38;5;63mSERVER > " . get_class($pk) . "\e[m" . PHP_EOL;
                    }
                    socket_sendto($this->socket, $buffer, strlen($buffer), 0, $this->clientHost, $this->clientPort);
                } elseif ($source === $this->clientHost and $port === $this->clientPort) {
                    if (($pk = $this->readDataPacket($buffer)) !== null) {
                        echo "\e[38;5;203mCLIENT > " . get_class($pk) . "\e[m" . PHP_EOL;
                    }
                    socket_sendto($this->socket, $buffer, strlen($buffer), 0, $this->serverHost, $this->serverPort);
                } else {
                    continue;
                }
            }
        }
    }

    /**
     * This function receives a packet sent by the client or server
     * @param string $buffer
     * @return null|DataPacket
     */
    protected function readDataPacket(string $buffer): ?DataPacket
    {
        if (($packet = Pool::getPacketFromPool(ord($buffer{0}))) !== null) {
            $packet->buffer = $buffer;
            $packet->decode();
            if ($packet instanceof DATA_PACKET_4) {
                foreach ($packet->packets as $pk) {
                    if (($id = ord($pk->buffer{0})) === 0xfe) {
                        $batch = PacketPool::getPacket($pk->buffer);
                        if ($batch instanceof BatchPacket) {
                            @$batch->decode();
                            if ($batch->payload !== "" && is_string($batch->payload)) {
                                foreach ($batch->getPackets() as $buf) {
                                    $stole = PacketPool::getPacketById(ord($buf{0}));
                                    //Now there are a lot of forks, then some packages can be decrypted wrongly and this will cause an error.
                                    //Here you can disable some packages if they cause an errors
                                    if (!$stole instanceof UpdateAttributesPacket) {
                                        $stole->buffer = $buf;
                                        $stole->decode();
                                        return $stole;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return null;
    }

    /**
     * This function can send a packet to the server or client
     * @param DataPacket $packet
     * @param string     $host
     * @param int        $port
     */
    protected function writeDataPacket(DataPacket $packet, string $host, int $port): void
    {
        $batch = new BatchPacket;
        $batch->addPacket($packet);
        $batch->setCompressionLevel(7);
        $batch->encode();

        $encapsulated = new EncapsulatedPacket;
        $encapsulated->reliability = 0;
        $encapsulated->buffer = $batch->buffer;

        $dataPacket = new DATA_PACKET_4;
        $dataPacket->seqNumber = 666;
        $dataPacket->packets = [$encapsulated];
        $dataPacket->encode();

        socket_sendto($this->socket, $dataPacket->buffer, strlen($dataPacket->buffer), 0, $host, $port);
    }
}