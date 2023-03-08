<?php

namespace core\Lib\ModelProto;

abstract class ALoadable implements ICanToJSON{
	
	public static function ListToJSON($list){
		return array_map(function($p){ return $p->toJSON(); }, $list); 
	}
	
	protected function loadField($info, $field, $default){
		$this->{$field} = isset($info->{$field})? $info->{$field}: $default;
		return $this;
	}
	
}