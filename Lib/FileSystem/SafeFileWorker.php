<?php

namespace core\Lib\FileSystem;

class SafeFileWorker{
	
	private const OVERWRITES_COUNT = 7;
	
	public static function Move($source, $target){
		if(copy($source, $target)){
			return static::Remove($source);
		}
		return false;
	}
	
	public static function Remove($file){
		if(!file_exists($file)) return true;
		$size = filesize($file);
		if($size===false) return false;
		for($i = 0; $i<static::OVERWRITES_COUNT; $i++){
			if(file_put_contents($file, self::getMask($size + rand(168, 2046)))===false){
				return false;
			}
		}
		return unlink($file);
		
	}
	
	private static function getMask($size, $iteration){
		switch($iteration){
			case 0:
				$template = char(0xFF);
				break;
			case 2:
				$template = char(0x00);
				break;
			case 4:
				$template = char(0xAA);
				break;
			case 6:
				$template = char(0x55);
				break;
			default:
				return random_bytes($size);
		}
		return str_repeat($template, $size);
	}
	
}