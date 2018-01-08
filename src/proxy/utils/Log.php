<?php

declare(strict_types=1);

namespace proxy\utils;


use pocketmine\utils\Terminal;

class Log{
	public static function Warn(string $message) : void{
		echo Terminal::$COLOR_YELLOW . $message . Terminal::$FORMAT_RESET . PHP_EOL;
	}

	public static function Success(string $message) : void{
		echo Terminal::$COLOR_GREEN . $message . Terminal::$FORMAT_RESET . PHP_EOL;
	}

	public static function Error(string $message) : void{
		echo Terminal::$COLOR_RED . $message . Terminal::$FORMAT_RESET . PHP_EOL;
	}

	public static function Info(string $message) : void{
		echo Terminal::$COLOR_AQUA . $message . Terminal::$FORMAT_RESET . PHP_EOL;
	}

	public static function Debug(string $message) : void{
		echo Terminal::$COLOR_GRAY . $message . Terminal::$FORMAT_RESET . PHP_EOL;
	}
}