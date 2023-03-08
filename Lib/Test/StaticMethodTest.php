<?php 

namespace core\Lib\Test;

class StaticMethodTest extends MethodTest{
	
	private $className;
	
	public function __construct($className, $methodName, $methodArguments, $assertedResult){
		parent::__construct($methodName, $methodArguments, $assertedResult);
		$this->className = $className;
	}
	
	public function oneMoreSameMethod($methodArguments, $assertedResult){
		return new self($this->className, $this->methodName, $methodArguments, $assertedResult);
	}
		
	public function oneMoreSameClass($methodName, $methodArguments, $assertedResult){
		return new self($this->className, $methodName, $methodArguments, $assertedResult);
	}
		
	protected function action(){
		$result = ($this->className)::{$this->methodName}(...$this->methodArguments);
		$this->checkResult($result);
	}

	
}