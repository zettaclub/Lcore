<?php

namespace core\Lib\Database;

class KeysPair{
	
	#prop objKey vgS c0
	private $objKey;
	#prop dbKey vgS c1
	private $dbKey;
	#prop dataType vgS
	private $dataType;
	#prop def vgS
	private $def;
	
	public function __construct($objKey, $dbKey, $dataType, $def = null){
		$this->objKey = $objKey;
		$this->dbKey = $dbKey;
		$this->dataType = strtoupper($dataType);
		$this->def = $def;
	}
	
	#gen - begin
	public function getObjKey(){ return $this->objKey; }
	public function getDbKey(){ return $this->dbKey; }
	public function getDataType(){ return $this->dataType; }
	public function getDef(){ return $this->def; }

	#gen - end
}