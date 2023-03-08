<?php

namespace core\Lib\Database;

use core\Lib\Collections\QArray;

class QOrConditionComb extends AQConditionComb{
	
	#prop subs vgS tqa
	private $subs;
	
	public function getSql(){
		return "(".$this->subs->implode(' OR ', function($c){ return $c->getSql(); }).")";
	}

	public function __construct($cond1, $cond2){
		$this->subs = QArray::Q([$cond1, $cond2]);
	}
	
	public function and($cond){
		return AQConditionComb::Or($this, $cond);
	}
	
	public function or($cond){
		$this->subs->push($cond);
		return $this;
	}
	
	public function not(){
		return AQConditionComb::Not($this);
	}
	
	
	#gen - begin
	public function getSubs(){ return $this->subs; }

	#gen - end
}