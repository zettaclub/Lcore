<?php 

namespace core\Lib\Database;

use core\Lib\App\App;
use core\Lib\Exceptions\InternalException;
use core\Lib\Exceptions\MysqlException;

class DbUtil{
	
	private static $cache = [];
	
	public static function Conn($name = 'def'){
		if(isset(self::$cache[$name])) return self::$cache[$name];
		if(!App::I()->hasModule('db')){
			throw new InternalException('Module db not configured');
		}
		$module = App::I()->getModule('db');
		if(!$module->hasParam($name)){
			throw new InternalException("Module db don`t contains connection {$name}");
		}
		$params = $module->getParam($name);
		if(is_string($params)){
			if(!$module->hasParam($params)){
				throw new InternalException("Module db don`t contains connection {$params} that connection {$name} is referred");
			}
			$params = $module->getParam($params);
		}
		if(!is_array($params)){
			throw new InternalException("Module db don`t contains wrond connection {$params} configuration");
		}
		$db = new \mysqli($params['host'], $params['user'], $params['password'], $params['db'], $params['port']);
		if ($db->connect_errno) {
			throw new InternalException('Error connection mysqli: ' . $db->connect_error);
		}
		self::$cache[$name] = $db;
		$db->set_charset($params['charset']);
		if ($db->connect_errno) {
			throw new InternalException('Error connection mysqli: ' . $db->connect_error);
		}
		return $db;
	}
	
	public static function BeginTransaction($conn = 'def'){
		$db = self::Conn($conn);
		return $db->begin_transaction();
	}
	
	public static function Commit($conn = 'def'){
		$db = self::Conn($conn);
		return $db->commit();
	}
	
	public static function Rollback($conn = 'def'){
		$db = self::Conn($conn);
		return $db->rollback();
	}
	
	public static function Escape($str, $conn = 'def'){
		$db = self::Conn($conn);
		return $db->escape_string($str);
	}
	
	public static function Query($sql, $conn = 'def'){
		$db = self::Conn($conn);
		$db->query($sql);
		if($db->errno>0) throw new MysqlException($db->error, $sql);
	}
	
	public static function Insert($sql, $conn = 'def'){
		$db = self::Conn($conn);
		$db->query($sql);
		if($db->errno>0) throw new MysqlException($db->error, $sql);
		return $db->insert_id;
	}
	
	public static function Update($sql, $conn = 'def'){
		$db = self::Conn($conn);
		$db->query($sql);
		if($db->errno>0) throw new MysqlException($db->error, $sql);
		return $db->affected_rows;
	}
	
	public static function Delete($sql, $conn = 'def'){
		$db = self::Conn($conn);
		$db->query($sql);
		if($db->errno>0) throw new MysqlException($db->error, $sql);
		return $db->affected_rows;
	}
	
	private static function GetQr($sql, $conn){
		$db = self::Conn($conn);
		$qr = $db->query($sql);
		if($db->errno>0) throw new MysqlException($db->error, $sql);
		return $qr;
	}
	
	public static function GetRows($sql, $conn = 'def'){
		$qr = self::GetQr($sql, $conn);
		$rows = [];
		while($row = $qr->fetch_assoc()){
			$rows[] = $row;
		}
		return $rows;
	}
	
	public static function GetRow($sql, $conn = 'def'){
		$qr = self::GetQr($sql, $conn);
		if($qr->num_rows>0) return $qr->fetch_assoc();
		return null;
	}
	
	public static function GetField($sql, $field, $conn = 'def'){
		$qr = self::GetQr($sql, $conn);
		if($qr->num_rows>0) return $qr->fetch_assoc()[$field];
		return null;
	}
	
	public static function GetArray($sql, $field, $conn = 'def'){
		$qr = self::GetQr($sql, $conn);
		$list = [];
		while($row = $qr->fetch_assoc()){
			$list[] = $row[$field];
		}
		return $list;
	}
	
	public static function GetDict($sql, $from, $to, $conn = 'def'){
		$qr = self::GetQr($sql, $conn);
		$list = [];
		while($row = $qr->fetch_assoc()){
			$list[$row[$from]] = $row[$to];
		}
		return $list;
	}
	
	public static function GetIntArray($sql, $field, $conn = 'def'){
		$qr = self::GetQr($sql, $conn);
		$list = [];
		while($row = $qr->fetch_assoc()){
			$list[] = (int)$row[$field];
		}
		return $list;
	}

	public static function GetIntField($sql, $field, $conn = 'def'){
		$res = self::GetField($sql, $field, $conn);
		return $res===null? null: (int)$res;
	}
	
	public static function GetId($sql, $conn = 'def'){
		return self::GetIntField($sql, 'id');
	}

	public static function GetFloatField($sql, $field, $conn = 'def'){
		$res = self::GetField($sql, $field, $conn);
		return $res===null? null: floatval($res);
	}
	
	public static function Close($name = null){
		if($name!==null){
			if(isset(self::$cache[$name])){
				self::$cache[$name]->close();
				unset(self::$cache[$name]);
			}
		}else{
			foreach(self::$cache as $db){
				$db->close();
			}
			self::$cache = [];
		}
	}
}