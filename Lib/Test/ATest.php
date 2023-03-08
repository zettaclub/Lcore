<?php 

namespace core\Lib\Test;

use core\Lib\Test\Interface\ITest;

abstract class ATest implements ITest{
	
	private $done = false;
	private $success;
	private $exception;
	private $printResult;
	
	protected abstract function action();
	
	
	public function run(){
		try{
			$this->action();
			$this->success = true;
		}catch(\Exception $ex){
			$this->exception = $ex;
			$this->success = false;
		}
		$this->done = true;
		return $this->success;
	}
	
	public function simplePrint(){
		$this->run();
		if($this->success) var_dump("Ok");
		else {
			var_dump("Fail");
			if($this->exception instanceof TestException){
				var_dump($this->exception->getMessage());
			}
			else var_dump($this->exception);
		}
	}
	
	public function bundlePrint(){
		$this->run();
		if($this->success) $this->printResult = "Ok";
		else {
			$this->printResult = "Fail\n";
			if($this->exception instanceof TestException){
				$this->printResult .= $this->exception->getMessage();
			}
			else $this->printResult .= print_r($this->exception, true);
		}
	}
	
	public function getPrintResult(){
		return $this->printResult;
	}
	
	public function isSuccess(){
		return $this->success;
	}
	
	public function isDone(){
		return $this->done;
	}
	
	public function getError(){
		return $this->exception;
	}
}