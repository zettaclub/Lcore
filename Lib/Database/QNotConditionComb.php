<?php

namespace core\Lib\Database;

class QNotConditionComb extends AQConditionComb{
	
	#prop sub vgS 
	private $sub;
	
	public function getSql(){
		return "NOT ".$this->sub->getSql();
	}
	
	public function __construct($cond){
		$this->sub = $cond;
	}
	
	public function and($cond){
		return AQConditionComb::GoAnd($this, $cond);
	}
	
	public function or($cond){
		return AQConditionComb::GoOr($this, $cond);
	}
	
	public function not(){
		return $this->sub;
	}
	
	
	#gen - begin
	public function getSub(){ return $this->sub; }

	#gen - end
}