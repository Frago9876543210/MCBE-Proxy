<?php

declare(strict_types=1);

namespace Frago9876543210\Example;


use pocketmine\network\mcpe\protocol\DataPacket;
use proxy\plugin\Plugin;
use proxy\utils\Log;

class Main extends Plugin{
	/**
	 * Called when the plugin is enabled
	 */
	public function onEnable() : void{
		Log::Warn("I am loaded!");
	}

	/**
	 * @param DataPacket $packet
	 */
	public function handleServerDataPacket(DataPacket $packet) : void{
		Log::Success(get_class($packet));
	}

	/**
	 * @param DataPacket $packet
	 */
	public function handleClientDataPacket(DataPacket $packet) : void{
		Log::Error(get_class($packet));
	}
}