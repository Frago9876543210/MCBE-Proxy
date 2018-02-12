<?php

declare(strict_types=1);

namespace proxy\hosts;

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\network\mcpe\protocol\SetPlayerGameTypePacket;
use pocketmine\network\mcpe\protocol\TextPacket;
use proxy\Proxy;

class Client extends BaseHost{
	/** @var bool $connected */
	public $connected;
	/** @var Vector3 $position */
	public $position;
	/** @var float $yaw */
	public $yaw;
	/** @var float $pitch */
	public $pitch;
	/** @var int $gamemode */
	public $gamemode;

	/**
	 * Client constructor.
	 * @param Proxy       $proxy
	 * @param null|string $address
	 * @param null|int    $port
	 * @param bool        $connected
	 */
	public function __construct(Proxy $proxy, $address, $port, bool $connected){
		parent::__construct($proxy, $address, $port);
		$this->connected = $connected;
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

	public function sendMessage(string $message) : void{
        $pk = new TextPacket();
        $pk->type = TextPacket::TYPE_RAW;
        $pk->message = $message;
        $this->dataPacket($pk);
    }

    public function sendTip(string $message): void{
        $pk = new TextPacket();
        $pk->type = TextPacket::TYPE_TIP;
        $pk->message = $message;
        $this->dataPacket($pk);
    }

    public function sendPopup(string $message) : void{
        $pk = new TextPacket();
        $pk->type = TextPacket::TYPE_POPUP;
        $pk->message = $message;
        $this->dataPacket($pk);
    }

    public function setGamemode(int $gamemode) : void{
        $pk = new SetPlayerGameTypePacket();
        $pk->gamemode = $gamemode;
        $this->dataPacket($pk);
    }
}