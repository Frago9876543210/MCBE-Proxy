<?php

declare(strict_types=1);

namespace proxy\utils;


use raklib\utils\InternetAddress;

class Address extends InternetAddress{
	public function __construct(string $address, int $port){
		parent::__construct(gethostbyname($address), $port, 4);
	}
}