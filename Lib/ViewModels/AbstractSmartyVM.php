<?php

namespace core\Lib\ViewModels;

abstract class AbstractSmartyVM implements \ArrayAccess{
	
	public function offsetExists($offset){
		return isset($this->{$offset});
	}

    public function offsetGet($offset){
		return $this->offsetExists($offset)? $this->{$offset}: null;
	}

    public function offsetSet($offset, $value){
		if (is_null($offset)) {
			throw new \Exception("Illigal operation");
		} else {
			if($this->offsetExists($offset)) $this->{$offset} = $value;
			else throw new \Exception("Illigal offset {$offset}");
		}
	}

    public function offsetUnset($offset){
		throw new \Exception("Illigal operation");
	}
	
}