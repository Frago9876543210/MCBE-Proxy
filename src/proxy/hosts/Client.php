<?php

declare(strict_types=1);

namespace proxy\hosts;

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\network\mcpe\protocol\SetPlayerGameTypePacket;
use pocketmine\network\mcpe\protocol\TextPacket;
use proxy\Proxy;
use proxy\utils\Address;

class Client extends BaseHost{
	/** @var bool $connected */
	public $connected = false;
	/** @var Vector3 $position */
	public $position;
	/** @var float $yaw */
	public $yaw;
	/** @var float $pitch */
	public $pitch;

	public function __construct(Proxy $proxy, ?Address $address){
		parent::__construct($proxy, $address);
	}

	/**
	 * @param DataPacket $packet
	 */
	public function handleDataPacket(DataPacket $packet) : void{
		if($packet instanceof MovePlayerPacket){
			$packet->decode();
			$this->position = $packet->position;
			$this->yaw = $packet->yaw;
			$this->pitch = $packet->pitch;
		}
	}

	public function sendMessage(string $message, int $type = TextPacket::TYPE_RAW) : void{
		$pk = new TextPacket;
		$pk->type = $type;
		$pk->message = $message;
		$pk->source = "";
		$this->dataPacket($pk);
	}

	public function setGamemode(int $gamemode) : void{
		$pk = new SetPlayerGameTypePacket;
		$pk->gamemode = $gamemode;
		$this->dataPacket($pk);
	}
}