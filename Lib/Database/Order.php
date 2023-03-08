<?php

namespace core\Lib\Database;

class Order{
	
	#prop field vgs
	private $field;
	#prop desc vgs
	private $desc;
	
	public function __construct($field, $desc){
		$this->field = $field;
		$this->desc = $desc;
	}
	
	public static function Descending($field){
		return new self($field, true);
	}
	
	public static function Ascending($field){
		return new self($field, false);
	}
	
	public function getSql(){
		return $this->field->getSql().($this->desc? " DESC": " ASC");
	}
	
	#gen - begin
	public function getField(){ return $this->field; }
	public function setField($field){ $this->field = $field; return $this; }
	public function getDesc(){ return $this->desc; }
	public function setDesc($desc){ $this->desc = $desc; return $this; }
	#gen - end
}