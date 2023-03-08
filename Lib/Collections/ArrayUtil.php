<?php 

namespace core\Lib\Collections;

class ArrayUtil{
	
	public static function Mix($array){
		$result = [];
		while(count($array)>0){
			$result []= array_splice($array, rand(0, count($array)-1), 1)[0];
		}
		return $result;
	}
	
	public static function RemoveAt($array, $position){
		array_splice($array, $position, 1);
		return $array;
	}
	
	public static function IndexOf($array, $needle){
		if(is_callable($needle)){
			foreach($array as $i=>$item)
				if($needle($item)) return $i;
		}else{
			foreach($array as $i=>$item)
				if($item==$needle) return $i;			
		}
		return null;
	}
	
}