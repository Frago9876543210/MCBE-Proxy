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
	 * @param DataPacket $packet
	 * @return bool
	 */
	abstract public function handleServerDataPacket(DataPacket $packet) : bool;

	/**
	 * @param DataPacket $packet
	 * @return bool
	 */
	abstract public function handleClientDataPacket(DataPacket $packet) : bool;
}