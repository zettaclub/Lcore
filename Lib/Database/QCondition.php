<?php

namespace core\Lib\Database;

class QCondition extends AExpression implements ICanBeCondition{
	
	#prop template vGS
	private $template;
	#prop a vGS 
	private $a;
	#prop b vGS 
	private $b;
	#prop c vGS 
	private $c;
	
	protected function __construct($template, $a, $b = null, $c = null){
		$this->template = $template;
		$this->a = $a;
		$this->b = $b;
		$this->c = $c;
	}
	
	public function getSql(){
		$a = $this->getParamSql($this->a);
		$b = $this->getParamSql($this->b);
		$c = $this->getParamSql($this->c);
		return str_replace(['#a', '#b', '#c'], [$a, $b, $c], $this->template);
	}
	
	public function and($cond){
		return AQConditionComb::GoAnd($this, $cond);
	}
	
	public function or($cond){
		return AQConditionComb::GoOr($this, $cond);
	}
	
	public function not(){
		return AQConditionComb::GoNot($this);
	}
	
	public static function IsNull($a){ 
		return new self("#a IS NULL", $a);
	}
	
	public static function IsNotNull($a){
		return new self("#a IS NOT NULL", $a);
	}
	
	public static function Equals($a, $b){ 
		return new self("#a=#b", $a, $b);
	}
	
	public static function NotEquals($a, $b){ 
		return new self("#a!=#b", $a, $b);
	}
	
	public static function More($a, $b){ 
		return new self("#a>#b", $a, $b);
	}
	
	public static function NotMore($a, $b){ 
		return new self("#a<=#b", $a, $b);
	}
	
	public static function Less($a, $b){ 
		return new self("#a<#b", $a, $b);
	}
	
	public static function NotLess($a, $b){ 
		return new self("#a>=#b", $a, $b);
	}
	
	public static function In($a, $b){ 
		return new self("#a IN #b", $a, $b);
	}
	
	public static function NotIn($a, $b){ 
		return new self("#a NOT IN #b", $a, $b);
	}
	
	public static function Between($a, $b, $c){ 
		return new self("#a BETWEEN #b AND #c", $a, $b, $c);
	}
	
	public static function NotBetween($a, $b, $c){ 
		return new self("#a NOT BETWEEN #b AND #c", $a, $b, $c);
	}
	
	public static function Like($a, $b){ 
		return new self("#a LIKE '%#b%'", $a, $b);
	}
	
	public static function StartsWith($a, $b){ 
		return new self("#a LIKE '#b%'", $a, $b);
	}
	
	public static function EndsWith($a, $b){ 
		return new self("#a LIKE '%#b'", $a, $b, $c);
	}
	
	public static function NotLike($a, $b){ 
		return new self("#a NOT LIKE '%#b%'", $a, $b);
	}
	
	public static function NotStartsWith($a, $b){ 
		return new self("#a NOT LIKE '#b%'", $a, $b);
	}
	
	public static function NotEndsWith($a, $b){ 
		return new self("#a NOT LIKE '%#b'", $a, $b, $c);
	}
	
	public function asc(){
		return Order::Ascending($this);
	}

	public function desc(){
		return Order::Descending($this);
	}

	
	public function getParamSql($p){
		if($p===null) return "NULL";
		if($p instanceof AValuesPair)
			return $p->getSql();
		if(is_string($p))
			return "'".DbUtil::Escape($p)."'";
		if(is_int($p) || is_float($p)) return $p;
		if(is_bool($p)) return (int)$p;
		if(is_array($p)){
			return '('.implode(', ', array_map(function($t){ return $this->getParamSql($t); }, $p)).')';
		}
		throw new \Exception('Unsuported data type');
	}
	
	public function asCondition(){
		return $this;
	}
	

	
	#gen - begin


	#gen - end
}