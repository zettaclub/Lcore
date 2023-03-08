<?php

namespace core\Lib\Database;

class FloatValuesPair extends AValuesPair{
	
	protected function convertDbToObj($value){
		return $value===null? null: floatval($value);
	}
	
	protected function convertObjToDb($value){
		return $value===null? "NULL": floatval($value);
	}
	
	protected function _getDbValueExpression(){
		return floatval($this->value);
	}

	public function setValue($value){ 
		if($value===null) $this->value = $value;
		else $this->value = floatval($value);
		return $this;
	}
	
}