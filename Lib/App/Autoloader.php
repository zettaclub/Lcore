<?php

namespace core\Lib\App;

use core\Lib\FileSystem\Path;

class Autoloader{
	
	private static $paths = [];
	
	public static function updateAutolader(){
		self::$paths = [
			"core" => App::I()->getCorePath(),
			"lib" => App::I()->getProjectPath('lib')
		];
		$auto = App::I()->hasModule("auto")? App::I()->getModule("auto")->getParams(): [];
		spl_autoload_unregister('__base_autoload');
		foreach($auto as $module=>$file){
			if(file_exists($file)) require_once $file;
			else throw new \Exception("Fail to load module {$module}, file {$file} not found");
		}
		spl_autoload_register([__CLASS__, 'autoload'], true);
	}
	
	public static function autoload($class) {
		$trimmed = trim($class, '\\');
		$key = explode('\\', $trimmed)[0];
		if(isset(self::$paths[$key])){
			$file = self::$paths[$key].DIRECTORY_SEPARATOR.str_replace('\\', DIRECTORY_SEPARATOR, substr($trimmed, strlen($key))).'.php';
			if (file_exists($file)) require_once $file;
			else throw new \Exception("Class {$class} not found in {$file}");
		}
	}
}