<?php

namespace core\Lib\Database;

abstract class AValuesPair extends AExpression implements ICanBeCondition{
	
	#prop value vgs a aprot
	protected $value;
	#prop oldValue vg aprot
	protected $oldValue;
	#prop keyPair vGS aprot
	protected $keyPair;
	#prop fieldSet vgs aprot
	protected $fieldSet;
	#prop objKey vgS rkeyPair
	#prop dbKey vgS rkeyPair
	
	public function __construct(KeysPair $keyPair){
		$this->keyPair = $keyPair;
		$this->value = $keyPair->getDef();
		$this->oldValue = $keyPair->getDef();
	}
	
	public static function Get($keyPair){
		switch($keyPair->getDataType()){
			case "TINYINT":
			case "SMALLINT":
			case "MEDIUMINT":
			case "INT":
			case "BIGINT":
				return new IntValuesPair($keyPair);
			case "DECIMAL":
			case "NUMERIC":
				return new FloatValuesPair($keyPair);
			default:
				return new TextValuesPair($keyPair);
		}
	}
	
	protected abstract function _getDbValueExpression();
	
	public function getDbValueExpression(){
		if($this->value===null) return "NULL";
		return $this->_getDbValueExpression();
	}
	
	protected function convertDbToObj($value){
		return $value;
	}
	
	protected function convertObjToDb($value){
		return $value;
	}
	
	public function getSelectKey(){
		return $this->fieldSet->getAlias()
					? $this->fieldSet->getAlias().".".$this->keyPair->getDbKey()
					: $this->keyPair->getDbKey();
	}
	
	public function loadFromDbRow($row){
		$this->value = $this->convertDbToObj($row[$this->getSelectKey()]);
		$this->oldValue = $this->value;
	}
	
	public function isInvalid(){
		return $this->value!==$this->oldValue;
	}
	
	public function getSql(){
		if($this->fieldSet!==null && $this->fieldSet->getAlias()!==null){
			return "`{$this->fieldSet->getAlias()}`.`{$this->keyPair->getDbKey()}`";
		}
		return "`{$this->keyPair->getDbKey()}`";
	}
	
	public function isNull(){ 
		return QCondition::IsNull($this);
	}

	public function isNotNull(){ 
		return QCondition::IsNotNull($this);
	}
	
	public function equals($to){ 
		return QCondition::Equals($this, $to);
	}

	public function notEquals($to){ 
		return QCondition::NotEquals($this, $to);
	}
	
	public function more($than){ 
		return QCondition::More($this, $than);
	}

	public function notMore($than){ 
		return QCondition::NotMore($this, $than);
	}

	public function less($than){ 
		return QCondition::Less($this, $than);
	}

	public function notLess($than){ 
		return QCondition::NotLess($this, $than);
	}

	public function in($list){ 
		return QCondition::In($this, $list);
	}

	public function notIn($list){ 
		return QCondition::NotIn($this, $list);
	}

	public function between($low, $high){ 
		return QCondition::Between($this, $low, $high);
	}

	public function notBetween($low, $high){ 
		return QCondition::NotBetween($this, $low, $high);
	}	
	
	public function asCondition(){
		if($this->value===null) return $this->isNull();
		return $this->equals($this->value);
	}
	
	public function asc(){
		return Order::Ascending($this);
	}

	public function desc(){
		return Order::Descending($this);
	}

	public function getSetRule(){
		return "{$this->getSql()} = {$this->getDbValueExpression()}";
	}
	
	public function getSelectRule(){
		return "{$this->getSql()} AS `{$this->getSelectKey()}`";
	}
	
	public function apply(){
		$this->oldValue = $this->value;
		return;
	}
	
	public function revert(){
		$this->value = $this->oldValue;
		return;
	}
		
	#gen - begin
	public function getValue(){ return $this->value; }
	public function setValue($value){ $this->value = $value; return $this; }
	public function getOldValue(){ return $this->oldValue; }
	protected function setOldValue($oldValue){ $this->oldValue = $oldValue; return $this; }
	public function getFieldSet(){ return $this->fieldSet; }
	public function setFieldSet($fieldSet){ $this->fieldSet = $fieldSet; return $this; }
	public function getObjKey(){ return $this->keyPair->getObjKey(); }
	public function getDbKey(){ return $this->keyPair->getDbKey(); }

	#gen - end
}