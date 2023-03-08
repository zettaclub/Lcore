<?php

namespace core\Lib\Database;

abstract class AExpression{
	
	public function as($name){
		return new NamedSelectField($this, $name);
	}
	
}