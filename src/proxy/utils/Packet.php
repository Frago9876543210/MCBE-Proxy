<?php

declare(strict_types=1);

namespace proxy\utils;


use pocketmine\network\mcpe\protocol\{
	BatchPacket, DataPacket, PacketPool
};
use proxy\hosts\BaseHost;
use raklib\protocol\{
	Datagram, EncapsulatedPacket
};

class Packet{
	/** @var int $number */
	public static $number = 0;

	/**
	 * @param string $buffer
	 * @return null|DataPacket
	 */
	public static function readDataPacket(string $buffer) : ?DataPacket{
		if($buffer{0} === "\x84"){
			$dataPacket = new Datagram;
			$dataPacket->buffer = $buffer;
			$dataPacket->decode();
			self::$number = $dataPacket->seqNumber + 1;
			foreach($dataPacket->packets as $encapsulatedPacket){
				/** @var BatchPacket $batch */
				if(($batch = PacketPool::getPacket($encapsulatedPacket->buffer)) instanceof BatchPacket){
					@$batch->decode();
					if($batch->payload !== "" && is_string($batch->payload)){
						foreach($batch->getPackets() as $buf){
							return PacketPool::getPacket($buf);
						}
					}
				}
			}
		}
		return null;
	}

	/**
	 * @param DataPacket $packet
	 * @param BaseHost   $baseHost
	 * @internal param int $seqNumber
	 */
	public static function writeDataPacket(DataPacket $packet, BaseHost $baseHost) : void{
		$batch = new BatchPacket;
		$batch->addPacket($packet);
		$batch->setCompressionLevel(7);
		$batch->encode();
		$encapsulated = new EncapsulatedPacket;
		$encapsulated->reliability = 0;
		$encapsulated->buffer = $batch->buffer;
		$dataPacket = new Datagram;
		$dataPacket->seqNumber = self::$number++;
		$dataPacket->packets = [$encapsulated];
		$dataPacket->encode();
		$baseHost->writePacket($dataPacket->buffer);
	}
}