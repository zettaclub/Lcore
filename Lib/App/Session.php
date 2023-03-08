<?php

namespace core\Lib\App;

class Session{
	
	private $sessionLoaded = false;
	
	private $values = [];
	
	private function loadValues($stayOpen = false){
		if($this->sessionLoaded) {
			if($stayOpen) session_start();
			return;
		}
		session_start();
		foreach($_SESSION as $key=>$value)
			$this->values[$key] = $value;
		$this->sessionLoaded = true;
		if(!$stayOpen) session_write_close();
	}
	
	public function isSetted($key){
		$this->loadValues();
		return isset($this->values[$key]);
	}
	
	public function getValue($key, $default = null){
		$this->loadValues();
		return isset($this->values[$key])
					? $this->values[$key]
					: $default;
	}
	
	public function setValue($key, $value){
		$this->loadValues(true);
		$this->values[$key] = $value;
		$_SESSION[$key] = $value;
		session_write_close();
		return $this;
	}
	
	public function unsetValue($key){
		$this->loadValues(true);
		unset($this->values[$key]);
		unset($_SESSION[$key]);
		session_write_close();
	}
	
	public function destroy(){
		session_destroy();
	}
}