<?php

declare(strict_types=1);

namespace Frago9876543210\Fly;


use pocketmine\network\mcpe\protocol\AdventureSettingsPacket;
use pocketmine\network\mcpe\protocol\DataPacket;
use proxy\plugin\ProxyPluginBase;
use proxy\Proxy;

class FlyPlugin extends ProxyPluginBase
{
    public function onInit(Proxy $proxy): void
    {
    }

    public function onDataPacketFromClient(DataPacket $packet): bool
    {
        return true;
    }

    public function onDataPacketFromServer(DataPacket $packet): bool
    {
        if ($packet instanceof AdventureSettingsPacket) {
            $send = new AdventureSettingsPacket;
            $send->flags = 615;
            $send->commandPermission = 0;
            $send->flags2 = 31;
            $send->playerPermission = 1;
            $send->customFlags = 0;
            $send->entityUniqueId = $packet->entityUniqueId;
            $this->getProxy()->writeDataPacket($send, $this->getProxy()->clientHost, $this->getProxy()->clientPort);
        }
        return true;
    }

    public function onRakNetPacketFromClient(string $buffer): bool
    {
        return true;
    }

    public function onRakNetPacketFromServer(string $buffer): bool
    {
        return true;
    }
}