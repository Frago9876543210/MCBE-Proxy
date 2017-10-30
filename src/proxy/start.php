<?php

declare(strict_types=1);


namespace proxy {

    use pocketmine\utils\Config;

    if (is_file("vendor/autoload.php")) {
        /** @noinspection PhpIncludeInspection */
        require_once("vendor/autoload.php");
    } else {
        echo "Composer autoloader not found" . PHP_EOL;
        exit(1);
    }

    $directory = getcwd() . DIRECTORY_SEPARATOR;

    $config = new Config($directory . "proxy-config.json", Config::JSON, [
        'server' => 'example.com',
        'port' => 19132,
        'bind-port' => 19132
    ]);
    $conf = $config->getAll();

    new Proxy($conf['server'], $conf['port'], $conf['bind-port']);
}