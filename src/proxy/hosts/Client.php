<?php

declare(strict_types=1);

namespace proxy\hosts;


use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
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
}