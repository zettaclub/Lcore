<?php

namespace core\Lib\Log;

class InternalErrorsLog extends AbstractLog{
	protected static $path = "internal-errors";
	protected static $fullPath = null;
	
	protected static function prepareContent($info){
		return print_r($info, true);
	}

}
