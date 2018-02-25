<?php

declare(strict_types=1);

namespace proxy\hosts;


use pocketmine\network\mcpe\protocol\DataPacket;
use proxy\Proxy;
use proxy\utils\Address;
use proxy\utils\Packet;

abstract class BaseHost{
	/** @var Proxy $proxy */
	protected $proxy;
	/** @var string $address */
	public $address;

	/**
	 * BaseHost constructor.
	 * @param Proxy        $proxy
	 * @param null|Address $address
	 */
	public function __construct(Proxy $proxy, ?Address $address){
		$this->proxy = $proxy;
		$this->address = $address;
	}

	/**
	 * @return Proxy
	 */
	public function getProxy() : Proxy{
		return $this->proxy;
	}

	/**
	 * @param string $buffer
	 */
	public function writePacket(string $buffer) : void{
		$this->proxy->writePacket($buffer, $this->address->ip, $this->address->port);
	}

	/**
	 * @param DataPacket $packet
	 */
	public function dataPacket(DataPacket $packet) : void{
		Packet::writeDataPacket($packet, $this);
	}

	/**
	 * @param DataPacket $packet
	 */
	public function handleDataPacket(DataPacket $packet) : void{
	}
}