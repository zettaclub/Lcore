<?php

namespace core\Lib\Database;

class IntValuesPair extends AValuesPair{
	
	protected function convertDbToObj($value){
		return $value===null? null: (int)$value;
	}
	
	protected function convertObjToDb($value){
		return $value===null? "NULL": (int)$value;
	}
	
	protected function _getDbValueExpression(){
		return (int)$this->value;
	}

	
	public function setValue($value){ 
		if($value===null) $this->value = $value;
		else $this->value = (int)$value;
		return $this;
	}

	
}