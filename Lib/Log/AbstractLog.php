<?php

namespace core\Lib\Log;

use core\Lib\App\App;

abstract class AbstractLog{
	
	protected static $path;
	protected static $fullPath = null;
	
	public static function Log($info){
		if(static::$fullPath === null)
			static::$fullPath = App::I()->getWorkdirPath("logs/".static::$path.".log");
		file_put_contents(static::$fullPath, "[".date("Y-m-d H:i:s")."] ".static::prepareContent($info)."\n\n", FILE_APPEND);
	}
	
	protected static function prepareContent($info){
		return $info;
	}
	
}