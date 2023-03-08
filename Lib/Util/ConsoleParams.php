<?php

namespace core\Lib\Util;

class ConsoleParams{
	
	private $params;
	private $namedParams = [];
	
	public function __construct($params){
		$this->params = $params;
	}
	
	public function requireParam($i, $failMessage){
		if(isset($this->params[$i])){
			return $this->params[$i];
		}
		else throw new \Exception($failMessage);
	}
}