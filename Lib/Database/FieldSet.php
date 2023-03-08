<?php

namespace core\Lib\Database;

use core\Lib\Collections\QArray;

class FieldSet{
	
	#prop alias vgs
	private $alias;
	#prop tableName vgS
	private $tableName;
	#prop fieldsList vgS tqa
	private $fieldsList;
	#prop __cache vGS
	private $__cache;
	
	public function __construct($tableName, ...$fields){
		$this->tableName = $tableName;
		$this->fieldsList = QArray::Q($fields);
		$this->__cache = [];
		foreach($fields as $field){
			$field->setFieldSet($this);
			$this->__cache[$field->getObjKey()] = $field;
		}
	}
	
	public function loadFromDbRow($row){
		foreach($this->fieldsList as $field){
			$field->loadFromDbRow($row);
		}
	}
	
	public function __get($field){
		if(!isset($this->__cache[$field])) throw new \Exception("FieldSet dont contains field {$field}");
		return $this->__cache[$field];
	}
	
	#gen - begin
	public function getAlias(){ return $this->alias; }
	public function setAlias($alias){ $this->alias = $alias; return $this; }
	public function getTableName(){ return $this->tableName; }
	public function getFieldsList(){ return $this->fieldsList; }

	#gen - end
}