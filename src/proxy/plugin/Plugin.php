<?php

declare(strict_types=1);

namespace proxy\plugin;


use pocketmine\network\mcpe\protocol\DataPacket;
use proxy\Proxy;

abstract class Plugin{
	/** @var Proxy */
	protected $proxy;

	/**
	 * Plugin constructor.
	 * @param Proxy $proxy
	 */
	public function __construct(Proxy $proxy){
		$this->proxy = $proxy;
	}

	/**
	 * Called when the plugin is enabled
	 */
	abstract public function onEnable() : void;

	/**
	 * @return Proxy
	 */
	public function getProxy() : Proxy{
		return $this->proxy;
	}

	/**
	 * @param DataPacket $packet
	 */
	abstract public function handleServerDataPacket(DataPacket $packet) : void;

	/**
	 * @param DataPacket $packet
	 */
	abstract public function handleClientDataPacket(DataPacket $packet) : void;
}