<?php

declare(strict_types=1);

namespace proxy\plugin;


use pocketmine\network\mcpe\protocol\DataPacket;
use proxy\Proxy;

abstract class ProxyPluginBase
{
    /**
     * @param Proxy $proxy
     */
    abstract public function onInit(Proxy $proxy): void;

    /**
     * @param DataPacket $packet
     * @return bool
     */
    abstract public function onDataPacketFromClient(DataPacket $packet): bool;

    /**
     * @param DataPacket $packet
     * @return bool
     */
    abstract public function onDataPacketFromServer(DataPacket $packet): bool;

    /**
     * @param string $buffer
     * @return bool
     */
    abstract public function onRakNetPacketFromClient(string $buffer): bool;

    /**
     * @param string $buffer
     * @return bool
     */
    abstract public function onRakNetPacketFromServer(string $buffer): bool;

    /**
     * @return Proxy
     */
    public function getProxy(): ?Proxy
    {
        return Proxy::getInstance();
    }
}