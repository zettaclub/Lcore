<?php

namespace core\Lib\Util;

use core\Lib\App\App;

class FileLocker{
	
	private $path;
	private $res;
	
	public function __construct($key){
		$this->path = App::I()->getWorkdirPath("lock/{$key}");
		if(!file_exists($this->path)){
			file_put_contents($this->path, "");
		}
	}
	
	public static function Run($key, $callable, $defaultResult = null){
		$lock = new self($key);
		if($lock->tryLock()){
			try{
				return $callable();
			}catch(\Exception $ex){
				throw $ex;
			}finally{
				$lock->unlock();
			}
		}else return $defaultResult;
	}
	
	public function tryLock(){
		$this->res = fopen($this->path, "r+");
		if(flock($this->res, LOCK_EX | LOCK_NB)){
			return true;
		}
		fclose($this->res);
		return false;
	}
	
	public function unlock(){
    	flock($this->res, LOCK_UN);
		fclose($this->res);
	}
}