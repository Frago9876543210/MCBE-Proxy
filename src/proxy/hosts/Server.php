<?php

declare(strict_types=1);

namespace proxy\hosts;


use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\SetPlayerGameTypePacket;
use pocketmine\network\mcpe\protocol\StartGamePacket;

class Server extends BaseHost{
	public function handleDataPacket(DataPacket $packet) : void{
		if($packet instanceof StartGamePacket){
			$packet->decode();
			$this->getProxy()->getClient()->gamemode = $packet->playerGamemode;
		}elseif($packet instanceof SetPlayerGameTypePacket){
			$packet->decode();
			$this->getProxy()->getClient()->gamemode = $packet->gamemode;
		}
	}
}