<?php

namespace core\Lib\ModelProto;

use core\Lib\Database\QWork;
use core\Lib\ModelProto\ICanToJSON;

abstract class ABaseModel implements ICanToJSON{
	
	protected static $tableName = "";
	protected static $keySetKeys = [];
	protected static $setAiOnInsert = null;
	protected static $stopJson = [];
	#prop fieldSet vg aprot
	protected $fieldSet;
	#prop keysSet vg aprot
	protected $keysSet;
	#prop _loaded vGS aprot tbool
	protected $_loaded = false;
	
	public function __construct(){
		$this
			->setFieldSet(static::GetFieldsSet())
			->bindFieldSet();
	}
	
	public static function GetFieldsSet(){
		return new FieldSet(self::$tableName);
	}
	
	protected function getKeySet(){
		return array_map(function($objKey){
			return $this->{$objKey};
		}, static::$keySetKeys);
	}

	protected function loadFromDbRow($row){
		foreach($this->fieldSet->getFieldsList() as $field){
			$field->loadFromDbRow($row);
		}
		$this->_loaded = true;
		return $this;
	}
	
	protected function updateFrom($source){
		if(is_array($source)){
			foreach($this->fieldSet->getFieldsList() as $field){
				if(isset($source[$field->getObjKey()]))
					$field->setValue($source[$field->getObjKey()]);
			}
		}else{
			foreach($this->fieldSet->getFieldsList() as $field){
				if(isset($source->{$field->getObjKey()}))
					$field->setValue($source->{$field->getObjKey()});
			}
		}
		return $this;
	}
	
	private function bindFieldSet(){
		foreach($this->fieldSet->getFieldsList() as $field){
			$this->{$field->getObjKey()} = $field;
		}
	}
	
	private function getPkCondition(){
		$result = null;
		foreach($this->getKeySet() as $field){
			if($result===null) $result = $field->asCondition();
			else $result = $result->and($field);
		}
		return $result;
	}
	
	public static function New(){
		return new static();
	}
	
	public static function GetAll(){
		$rows = static::Q($set)->select();
		return static::DbLoadList($rows);
	}
	
	public static function DbLoad($row){
		$result = new static();
		return $result->loadFromDbRow($row);
	}
	
	public static function DbLoadList($rows){
		$results = [];
		foreach($rows as $row){
			$result = new static();
			$results[] = $result->loadFromDbRow($row);
		}
		return $results; 
	}
	
	public function save(){
		if($this->_loaded) return $this->update();
		return $this->insert();
	}
	
	public function reload(){
		$row = $this->query($set)->where($this->getPkCondition())->selectOne(...$set->getFieldsList());
		$this->loadFromDbRow($row);
		return $this;
	}
	
	public function delete(){
		$this->beforeDelete();
		$this->query($set)->where($this->getPkCondition())->delete();
		$this->_loaded = false;
		$this->afterDelete();
		return $this;
	}
	
	protected function beforeDelete(){}
	protected function afterDelete(){}
	
	protected function insert(){
		$this->beforeInsert();
		$insertList = $this->fieldSet->getFieldsList()
			->where(function($field){ return $field->isInvalid(); });
		$result = $this->query($set)->insert(...$insertList);
		if(static::$setAiOnInsert!==null) $this->{static::$setAiOnInsert}->setValue($result)->apply();
		foreach($insertList as $field) $field->apply();
		$this->_loaded = true;
		$this->afterInsert();
		return $this;
	}
	
	protected function beforeInsert(){}
	protected function afterInsert(){}
	
	protected function update(){
		$this->beforeUpdate();
		$updateList = $this->fieldSet->getFieldsList()
			->where(function($field){ return $field->isInvalid(); });
		$this->query($set)->where($this->getPkCondition())->update(...$updateList);
		foreach($updateList as $field) $field->apply();
		$this->_loaded = true;
		$this->afterUpdate();
		return $this;
	}
	
	protected function beforeUpdate(){}
	protected function afterUpdate(){}
	
	protected function query(&$set){
		$set = $this->fieldSet;
		return QWork::Q()->on($set);
	}
		
	public static function Q(&$set){
		$set = static::GetFieldsSet();
		return QWork::Q()->on($set);
	}
	
	public static function T(&$set){
		$set = static::GetFieldsSet();
		return $set;
	}
		
	public function isLoaded(){ return $this->_loaded; }
	
	public function toJson($rules = []){
		$allKeys = count($rules)===0 || in_array('*', $rules);
		$hasStops = count(static::$stopJson)>0;
		$result = [];
		foreach($this->fieldSet->getFieldsList() as $field){
			if($allKeys || in_array($field->getObjKey(), $rules)){
				if($hasStops && in_array($field->getObjKey(), static::$stopJson))
					continue;
				$result[$field->getObjKey()] = $field->getValue();
			}
		}
		return $result;
	}
	
	#gen - begin
	public function getFieldSet(){ return $this->fieldSet; }
	protected function setFieldSet($fieldSet){ $this->fieldSet = $fieldSet; return $this; }
	public function getKeysSet(){ return $this->keysSet; }
	protected function setKeysSet($keysSet){ $this->keysSet = $keysSet; return $this; }

	#gen - end
}