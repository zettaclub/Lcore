<?php

namespace core\Lib\Log;

class MysqlLog extends AbstractLog{
	protected static $path = "mysql-errors";
	protected static $fullPath = null;

	protected static function prepareContent($info){
		return print_r($info, true);
	}

}
