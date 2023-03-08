<?php 

namespace core\Lib\Test;

use core\Lib\Collections\QArray;
use core\Lib\Test\Interface\ITest;

abstract class ATestsBundle{
	
	private $tests = [];
	private $name;
	
	public function __construct($name){
		$this->name = $name;
		$this->buildTests();
	}
	
	protected abstract function buildTests();
	
	protected function pushTest(ITest $test){
		$this->tests[]= $test;
		return $this;
	}
	
	public function run(){
		$success = true;
		foreach($this->tests as $test){
			$testSuccess = $test->run();
			$success = $success && $testSuccess;
		}
		return $success;
	}
	
	public function simplePrint(){
		foreach($this->tests as $test){
			$test->bundlePrint();
		}
		if(QArray::Q($this->tests)->all(function($p){ return $p->isSuccess(); })){
			echo "\n".$this->name."\n     - Ok\n\n";
		}else{
			echo "\n".$this->name."\n     - Fail\n";
			echo QArray::Q($this->tests)
					->where(function($p){ return !$p->isSuccess(); })
					->implode("\n", function($p){ return $p->getPrintResult(); });
		}
	}

}
