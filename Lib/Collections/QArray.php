<?php 

namespace core\Lib\Collections;

class QArray implements \ArrayAccess, \IteratorAggregate{
	private $innerArray;
	
	/**
	 * Constructor is private.
	 * Initialization is available throw fabric method only
	 **/
	private function __construct($innerArray){
		$this->innerArray = $innerArray; 
	}
	
	/**
	 * Public fabric method
	 **/
	public static function Q($array = []){
		return new self($array);
	}
	
	/**
	 * Duplicate of map for linq-style compatibility
	 **/
	public function select($callback){
		return $this->map($callback);
	}
	
	public function sorted($asc = true){
		$a = $this->innerArray;
		if($asc===true) sort($a);
		else rsort($a);
		return self::Q($a);
	}
	
	public function map($callback){
		return new self(array_map($callback, $this->innerArray));
	}
	
	public function where($callback){
		$result = [];
		foreach($this->innerArray as $i=>$item)
			if($callback($item, $i))
				$result []= $item;
		return new self($result);
	}
	
	public function any($callback){
		foreach($this->innerArray as $i=>$item)
			if($callback($item, $i)) return true;
		return false;
	}
	
	public function implode($delimiter="", $callback = null){
		if(is_callable($callback)){
			return implode($delimiter, $this->map($callback)->toArray());
		}
		else return implode($delimiter, $this->innerArray);
	}
	
	public static function Explode($delimiter, $string){
		return new self(explode($delimiter, $string));
	}
	
	public function all($callback){
		foreach($this->innerArray as $i=>$item)
			if(!$callback($item, $i)) return false;
		return true;
	}
	
	public function first(){
		if(count($this->innerArray)===0){
			throw new \Exception("Collection has no any elements");
		}
		return $this->innerArray[0];
	}
	
	public function last(){
		if(count($this->innerArray)===0){
			throw new \Exception("Collection has no any elements");
		}
		return $this->innerArray[count($this->innerArray)-1];
	}
	
	public function firstOrDefault($default = null){
		if(count($this->innerArray)===0){
			return $default;
		}
		return $this->innerArray[0];
	}
	
	public function lastOrDefault($default = null){
		if(count($this->innerArray)===0){
			return $default;
		}
		return $this->innerArray[count($this->innerArray)-1];
	}
	
	public function sum($callback = null){
		if(is_callable($callback)){
			$sum = 0;
			foreach($this->innerArray as $i=>$item)
				$sum += $callback($item, $i);
			return $sum;
		}
		else return array_sum($this->innerArray);
	}
	
	public function min($callback = null){
		if(is_callable($callback)){
			$mv = null;
			$mi = null; 
			foreach($this->innerArray as $i=>$item){
				$v = $callback($item, $i);
				if($mv===null || $v<$mv){
					$mv = $v;
					$mi = $i;
				}
			}
			return $mi===null? null: $this->innerArray[$mi];
		}
		else return min($this->innerArray);
	}
	
	public function max($callback = null){
		if(is_callable($callback)){
			$mv = null;
			$mi = null; 
			foreach($this->innerArray as $i=>$item){
				$v = $callback($item, $i);
				if($mv===null || $v>$mv){
					$mv = $v;
					$mi = $i;
				}
			}
			return $mi===null? null: $this->innerArray[$mi];
		}
		else return max($this->innerArray);
	}

	public function flatMap($callable = null){
		$result = [];
		if($callable===null){
			foreach($this->innerArray as $arr)
				$result = array_merge($result, $arr instanceof QArray? $arr->toArray(): $arr);
		}else{
			foreach($this->innerArray as $item){
				$arr = $callable($item);
				$result = array_merge($result, $arr instanceof QArray? $arr->toArray(): $arr);
			}
		}
		return new self(array_values($result));
	}
	
	public function removeAll($list){
		$toRemove = [];
		if(is_callable($list)){
			foreach($this->innerArray as $i=>$item){
				if($list($item, $i)) $toRemove []= $i;
			}
			$toRemove = array_reverse($toRemove);
		}else{
			foreach($list as $item){
				$pos = $this->indexOf($item);
				if($pos!==null) $toRemove []= $pos;
			}
			arsort($toRemove);
		}
		foreach($toRemove as $pos)
			$this->removeAt($pos);
		return $this;
	}
	
	public function remove($item){
		$position = $this->indexOf($item);
		if($position!==null) $this->removeAt($position);
		return $this;
	}
	
	public function removeAt($position){
		if($position<0 || $this->count()<=$position){
			throw new \Exception("Cannot remove from illigal position");
		}
		array_splice($this->innerArray, $position, 1);
		return $this;
	}
	
	public function contains($needle){
		return $this->indexOf($needle)!==null;
	}
	
	public function indexOf($needle){
		if(is_callable($needle)){
			foreach($this->innerArray as $i=>$item)
				if($needle($item)) return $i;
		}else{
			foreach($this->innerArray as $i=>$item)
				if($item==$needle) return $i;			
		}
		return null;
	}
	
	public function find($needle){
		if(is_callable($needle)){
			foreach($this->innerArray as $i=>$item)
				if($needle($item)) return $item;
		}else{
			foreach($this->innerArray as $i=>$item)
				if($item==$needle) return $item;			
		}
		return null;
	}
	
	public function push($item){
		$this->innerArray[] = $item;
		return $this;
	}
	
	public function pushRange($list){
		foreach($list as $item)
			$this->innerArray[] = $item;
		return $this;
	}
	
	public function insert($position, $item){
		if($position<0 || $this->count()<=$position){
			throw new \Exception("Cannot insert at illigal position");
		}
		array_splice($this->innerArray, $position, 0, [$item]);		
	}
	
	public function insertRange($position, $list){
		if($position<0 || $this->count()<=$position){
			throw new \Exception("Cannot insert at illigal position");
		}
		array_splice($this->innerArray, $position, 0, $list);		
	}
	
	public function count(){
		return count($this->innerArray);
	}
	
	public function isEmpty(){
		return count($this->innerArray)===0;
	}
	
	public function empty(){
		$this->innerArray = [];
		return $this;
	}
	
	public function copy(){
		return new self($this->innerArray);
	}
	
	public function uniq(){
		return new self(array_values(array_unique($this->innerArray)));
	}

	public function toArray(){
		return $this->innerArray;
	}
	
	public function offsetSet($offset, $value) {
        if (is_null($offset)) $this->innerArray[] = $value;
        else $this->innerArray[$offset] = $value;
    }

	public function offsetExists($offset) {
		return isset($this->innerArray[$offset]);
	}

	public function offsetUnset($offset) {
		unset($this->innerArray[$offset]);
	}

	public function offsetGet($offset) {
		return isset($this->innerArray[$offset]) ? $this->innerArray[$offset] : null;
	}
	
	public function getIterator(){
        return new \ArrayIterator($this->innerArray);
    }
	
}
