<?php

namespace core\Lib\Log;

class DevLog extends AbstractLog{
	protected static $path = "dev";
	protected static $fullPath = null;
	
	protected static function prepareContent($info){
		return print_r($info, true);
	}

}
