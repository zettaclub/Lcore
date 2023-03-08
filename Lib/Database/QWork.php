<?php

namespace core\Lib\Database;

use core\Lib\Collections\QArray;

class QWork{
	
	static $ab = "abcdefghijklmnopqtstuvwxyz";
	
	#prop bundle vgS tqa
	private $bundle;
	#prop orders vgS tqa
	private $orders;
	#prop aliasIndex vGS
	private $aliasIndex;
	#prop _where vGS
	private $_where;
	#prop _limit vgS
	private $_limit;
	#prop _offset vgS
	private $_offset;
	#prop _echoSql vGS
	private $_echoSql;
	
	private function __construct(){
		$this->bundle = QArray::Q();
		$this->orders = QArray::Q();
	}
	
	public static function Q(){
		return new self();
	}
	
	public function on($fieldSet){
		$this->bundle->push(['', $fieldSet, null]);
		return $this;
	}
	
	public function join($fieldSet, $condition = null){
		return $this->_join('JOIN', $fieldSet, $condition);
	}
	
	public function leftJoin($fieldSet, $condition = null){
		return $this->_join('LEFT JOIN', $fieldSet, $condition);
	}
	
	public function rightJoin($fieldSet, $condition = null){
		return $this->_join('RIGHT JOIN', $fieldSet, $condition);
	}
	
	public function straightJoin($fieldSet, $condition = null){
		return $this->_join('STRAIGHT JOIN', $fieldSet, $condition);
	}
	
	private function buildBundle(){
		return $this->bundle
			->implode("\n", function($item){
				list($type, $fs, $cond) = $item;
				$res = "{$type} `{$fs->getTableName()}`";
				if($fs->getAlias()!==null) $res .= " AS `{$fs->getAlias()}`";
				if($cond!==null) $res .= "ON {$cond->getSql()}";
				return $res;
			});
	}
	
	private function _join($type, $fieldSet, $condition){
		if($this->bundle->count()===1){
			$this->bundle->first()[1]->setAlias($this->getNextAlias());
		}
		$fieldSet->setAlias($this->getNextAlias());
		$this->bundle->push([$type, $fieldSet, $condition]);
		return $this;
	}
	
	private function getNextAlias(){
		if($this->aliasIndex===null) {
			$this->aliasIndex = 'a';
		}else{
			$this->aliasIndex = self::$ab[strpos($this->aliasIndex, self::$ab)+1];
		}
		return $this->aliasIndex;
	}
	
	public function where($where){
		if($this->_where===null) $this->_where = $where->asCondition();
		else $this->_where = $this->_where->and($where);
		return $this;
	}
	public function order($order){
		$this->orders->push($order);
		return $this;
	}
	
	private function getWhereSql(){
		if($this->_where===null) return "";
		return "WHERE ".$this->_where->getSql();
	}
	
	public function delete(...$fs){
		$sql = "DELETE \n"." FROM ".$this->buildBundle()."\n";
		$sql .= $this->getWhereSql();
		$sql .= $this->getOrderSql();
		$sql .= $this->getLimitSql();
		if($this->_echoSql) echo $sql;
		return DbUtil::Delete($sql);
	}
	
	public function update(...$fields){
		if(count($fields)===0) return true;
		$sql = "UPDATE \n".$this->buildBundle()."\n";
		$sql .= "SET ".implode(", ", array_map(function($p){ return $p->getSetRule(); }, $fields))."\n";
		$sql .= $this->getWhereSql();
		$sql .= $this->getOrderSql();
		$sql .= $this->getLimitSql();
		if($this->_echoSql) echo $sql;
		return DbUtil::Update($sql);
	}
	
	public function insert(...$fields){
		$sql = "INSERT INTO \n".$this->bundle->first()[1]->getTableName()."(".implode(", ", array_map(function($p){ return $p->getSql(); }, $fields)).")";
		$sql .= "VALUES (".implode(", ", array_map(function($p){ return $p->getDbValueExpression(); }, $fields)).")\n";
		if($this->_echoSql) echo $sql;
		return DbUtil::Insert($sql);
	}
	
	public function select(...$fields){
		if(count($fields)===0){
			$fields = $this->bundle->flatMap(function($item){ return $item[1]->getFieldsList(); });
		}else $fields = QArray::Q($fields);
		$sql = "SELECT ".$fields->implode(", ", function($p){ return $p->getSelectRule(); })."\n";
		$sql .= " FROM ".$this->buildBundle()."\n";
		$sql .= $this->getWhereSql();
		$sql .= $this->getOrderSql();
		$sql .= $this->getLimitSql();
		if($this->_echoSql) echo $sql;
		return DbUtil::GetRows($sql);
	}
	
	public function count(){
		$sql = "SELECT COUNT(*) AS `count`\n";
		$sql .= " FROM ".$this->buildBundle()."\n";
		$sql .= $this->getWhereSql();
		$sql .= $this->getOrderSql();
		$sql .= $this->getLimitSql();
		if($this->_echoSql) echo $sql;
		return DbUtil::GetIntField($sql, 'count');
	}
	
	public function selectOne(...$fields){
		if(count($fields)===0){
			$fields = $this->bundle->flatMap(function($item){ return $item[1]->getFieldsList(); });
		}else $fields = QArray::Q($fields);
		$this->_limit = 1;
		$sql = "SELECT ".$fields->implode(", ", function($p){ return $p->getSelectRule(); })."\n";
		$sql .= " FROM ".$this->buildBundle()."\n";
		$sql .= $this->getWhereSql();
		$sql .= $this->getOrderSql();
		$sql .= $this->getLimitSql();
		if($this->_echoSql) echo $sql;
		return DbUtil::GetRow($sql);
	}
	
	public function limit($offset, $limit = null){
		if($limit===null){
			$this->_limit = (int)$offset;
		}else{
			$this->_limit = (int)$limit;
			$this->_offset = (int)$offset;
		}
		return $this;
	}
	
	private function getOrderSql(){
		if($this->orders->isEmpty()) return "";
		return " ORDER BY ".$this->orders->implode(', ', function($order){
				return $order->getSql();
			});
	}
	
	private function getLimitSql(){
		if($this->_limit===null) return "";
		if($this->_offset===null) return " LIMIT {$this->_limit}\n";
		return " LIMIT {$this->_offset}, {$this->_limit}\n";
	}
	
	public function echoSql($do = true){
		$this->_echoSql = $do;
		return $this;
	}
	
	#gen - begin
	public function getBundle(){ return $this->bundle; }
	public function getOrders(){ return $this->orders; }
	public function get_limit(){ return $this->_limit; }
	public function get_offset(){ return $this->_offset; }

	#gen - end
}