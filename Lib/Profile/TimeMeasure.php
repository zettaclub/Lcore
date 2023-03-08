<?php

namespace core\Lib\Profile;

class TimeMeasure{
	private static $instance;
	#prop start vgS
	private $start;
	#prop last vgS
	private $last;
	
	public function __construct(){
		$this->start = microtime(true);
		$this->last = $this->start;
	}
	
	public function echoPoint($msg = ''){
		$now = microtime(true);
		if(strlen($msg)>0) $msg .= ' ';
		echo '<div>'.$msg.'[delta]: '.($now - $this->last).' ~~~ [from start]: '.($now - $this->start).'</div>';
		$this->last = $now;
	}
	
	protected static function I(){
		if(self::$instance===null) self::$instance = new self();
		return self::$instance;
	}
	
	public static function Mark(){
		self::I()->echoPoint();
	}
	
	public static function Init(){
		return self::I();
	}
	
	#gen - begin
	public function getStart(){ return $this->start; }
	public function getLast(){ return $this->last; }

	#gen - end
}