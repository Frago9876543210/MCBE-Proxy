<?php

declare(strict_types=1);

namespace proxy {

	use pocketmine\utils\Config;

	const COMPOSER = 'vendor/autoload.php';

	if(is_file(COMPOSER)){
		/** @noinspection PhpIncludeInspection */
		require_once(COMPOSER);
	}else{
		echo "[-] Composer autoloader not found." . PHP_EOL;
		exit(1);
	}

	$config = new Config("config.yml", Config::YAML, [
		'server-ip' => 'example.com',
		'server-port' => 19132,
		'interface' => '0.0.0.0',
		'bind-port' => 19132,
		'without-plugins' => false
	]);
	$settings = $config->getAll();
	new Proxy($settings['server-ip'], $settings['server-port'], $settings['interface'], $settings['bind-port'], $settings['without-plugins']);
}