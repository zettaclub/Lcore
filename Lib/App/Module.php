<?php

namespace core\Lib\App;

class Module{
	#prop params vgS
	private $params;
	
	public function __construct($params){
		$this->params = $params;
	}
	
	public function accept($params){
		$this->params = array_replace_recursive($this->params, $params);
	}
	
	public function getParam($key){
		return $this->params[$key];
	}
	
	public function hasParam($key){
		return isset($this->params[$key]);
	}
	
	#gen - begin
	public function getParams(){ return $this->params; }

	#gen - end
}