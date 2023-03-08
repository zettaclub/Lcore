<?php

namespace core\Lib\ModelProto;

class ToJSONUtil{
	
	public static function ToJson($list, $rules = []){
		return array_map(function($item)use($rules){
			return $item->toJson($rules);
		}, $list);
	}
	
}