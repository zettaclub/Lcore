<?php

namespace core\Lib\Database;

class NamedSelectField{
	
	#prop expression vgS
	private $expression;
	#prop name vgs
	private $name;
	
	public function __construct($expression, $name){
		$this->expression = $expression;
		$this->name = $name;
	}
	
	public function getSelectRule(){
		return "{$this->expression->getSql()} AS `{$this->name}`";
	}
	
	#gen - begin
	public function getExpression(){ return $this->expression; }
	public function getName(){ return $this->name; }
	public function setName($name){ $this->name = $name; return $this; }
	#gen - end
}