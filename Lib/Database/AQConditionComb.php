<?php

namespace core\Lib\Database;

abstract class AQConditionComb extends AExpression implements ICanBeCondition{
		
	abstract public function and($cond);
	abstract public function or($cond);
	abstract public function not();
	abstract public function getSql();
	
	
	public static function GoAnd($cond1, $cond2){
		return new QAndConditionComb($cond1->asCondition(), $cond2->asCondition());
	}
	
	public static function GoOr($cond1, $cond2){
		return new QOrConditionComb($cond1->asCondition(), $cond2->asCondition());
	}
	
	public static function GoNot($cond){
		return new QNotConditionComb($cond1->asCondition());
	}
	
	public function asCondition(){
		return $this;
	}
	
	public function asc(){
		return Order::Ascending($this);
	}

	public function desc(){
		return Order::Descending($this);
	}
	
}