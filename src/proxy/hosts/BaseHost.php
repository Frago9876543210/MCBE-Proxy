<?php

declare(strict_types=1);

namespace proxy\hosts;


use pocketmine\network\mcpe\protocol\DataPacket;
use proxy\Proxy;
use proxy\utils\Packet;

abstract class BaseHost{
	/** @var Proxy $proxy */
	protected $proxy;
	/** @var string $address */
	protected $address;
	/** @var int $port */
	protected $port;

	/**
	 * BaseHost constructor.
	 * @param Proxy  $proxy
	 * @param string $address
	 * @param int    $port
	 */
	public function __construct(Proxy $proxy, ?string $address, ?int $port){
		$this->proxy = $proxy;
		$this->address = $address !== null ? gethostbyname($address) : null;
		$this->port = $port;
	}

	/**
	 * @return Proxy
	 */
	public function getProxy() : Proxy{
		return $this->proxy;
	}

	/**
	 * @return string
	 */
	public function getAddress() : string{
		return $this->address;
	}

	/**
	 * @param string $address
	 */
	public function setAddress(string $address) : void{
		$this->address = gethostbyname($address);
	}

	/**
	 * @return int
	 */
	public function getPort() : int{
		return $this->port;
	}

	/**
	 * @param int $port
	 */
	public function setPort(int $port) : void{
		$this->port = $port;
	}

	/**
	 * @param string $buffer
	 */
	public function writePacket(string $buffer) : void{
		$this->proxy->writePacket($buffer, $this->address, $this->port);
	}

	/**
	 * @param string $address
	 * @param int    $port
	 * @return bool
	 */
	public function equals(string $address, int $port) : bool{
		return ($this->address !== null && $this->port !== null) ? $this->address === $address && $this->port === $port : false;
	}

	/**
	 * @param DataPacket $packet
	 */
	public function handleDataPacket(DataPacket $packet) : void{
	}

	/**
	 * @param DataPacket $packet
	 */
	public function dataPacket(DataPacket $packet) : void{
		Packet::writeDataPacket($packet, $this);
	}
}