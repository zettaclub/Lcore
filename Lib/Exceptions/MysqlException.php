<?php 

namespace core\Lib\Exceptions;

class MysqlException extends \Exception{
	
	private $sql;
	
	public function __construct($message, $sql){
		parent::__construct($message);
		$this->sql = $sql;
	}
	
	public function getSql(){ return $this->sql; }
}