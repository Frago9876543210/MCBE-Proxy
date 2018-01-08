<?php

declare(strict_types=1);

namespace proxy\hosts;


use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\SetPlayerGameTypePacket;
use pocketmine\network\mcpe\protocol\StartGamePacket;

class Server extends BaseHost{
	/** @var string $data */
	public $data;

	/**
	 * @param DataPacket $packet
	 */
	public function handleDataPacket(DataPacket $packet) : void{
		if($packet instanceof StartGamePacket){
			$packet->decode();
			$this->getProxy()->getClient()->gamemode = $packet->playerGamemode;
		}elseif($packet instanceof SetPlayerGameTypePacket){
			$packet->decode();
			$this->getProxy()->getClient()->gamemode = $packet->gamemode;
		}
	}

	/**
	 * Get motd from server
	 * @return null|string
	 */
	public function getName() : ?string{
		return isset($this->data[0]) ? $this->data[0] : null;
	}

	/**
	 * Get server protocol
	 * @return string|null
	 */
	public function getProtocol() : ?string{
		return isset($this->data[1]) ? $this->data[1] : null;
	}

	/**
	 * Get server version
	 * @return string|null
	 */
	public function getVersion() : ?string{
		return isset($this->data[2]) ? $this->data[2] : null;
	}
}