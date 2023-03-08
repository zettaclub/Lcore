<?php

namespace core\Lib\Database;

use core\Lib\Collections\QArray;

class QAndConditionComb extends AQConditionComb{
	
	#prop subs vgS tqa
	private $subs;
	
	public function getSql(){
		return "(".$this->subs->implode(' AND ', function($c){ return $c->getSql(); }).")";
	}
	
	public function __construct($cond1, $cond2){
		$this->subs = QArray::Q([$cond1, $cond2]);
	}
	
	public function and($cond){
		$this->subs->push($cond);
		return $this;
	}
	
	public function or($cond){
		return AQConditionComb::GoOr($this, $cond);
	}
	
	public function not(){
		return AQConditionComb::GoNot($this);
	}

		
	#gen - begin
	public function getSubs(){ return $this->subs; }

	#gen - end
}