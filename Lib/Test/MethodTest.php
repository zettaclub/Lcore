<?php 

namespace core\Lib\Test;

abstract class MethodTest extends ATest{
	
	protected $methodName;
	protected $methodArguments;
	protected $isExceptionAsserted = false;
	protected $assertedResult;
	
	public function __construct($methodName, $methodArguments, $assertedResult){
		$this->methodName = $methodName;
		$this->methodArguments = $methodArguments;
		$this->assertedResult = $assertedResult;
	}
		
	protected function checkResult($result){
		if(is_callable($this->assertedResult)){
			($this->assertedResult)($result);
		}elseif($result!==$this->assertedResult){
			throw new TestException("\nWrong result: ".print_r($result, true)."\n    expected: ".print_r($this->assertedResult, true));
		}
	}
	
}