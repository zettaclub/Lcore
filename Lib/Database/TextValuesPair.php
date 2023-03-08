<?php

namespace core\Lib\Database;

class TextValuesPair extends AValuesPair{
	
	protected function convertDbToObj($value){
		return $value;
	}
	
	protected function convertObjToDb($value){
		return $value===null? "NULL": DbUtil::Escape($value);
	}
	
	protected function _getDbValueExpression(){
		return "'".DbUtil::Escape($this->value)."'";
	}

	public function setValue($value){ 
		if($value===null) $this->value = $value;
		else $this->value = (string)$value;
		return $this;
	}
	
	public function like($what){ 
		return QLikeCondition::Like($this, $what);
	}
	
	public function startsWith($what){ 
		return QLikeCondition::StartsWith($this, $what);
	}
	
	public function endsWith($what){ 
		return QLikeCondition::EndsWith($this, $what);
	}
	
	public function notLike($what){ 
		return QLikeCondition::Like($this, $what);
	}
	
	public function notStartsWith($what){ 
		return QLikeCondition::StartsWith($this, $what);
	}
	
	public function notEndsWith($what){ 
		return QLikeCondition::EndsWith($this, $what);
	}

}