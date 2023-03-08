<?php 

namespace core\Lib\Test;

use core\Lib\Test\ObjectProviders\IObjectProvider;

class DynamicMethodTest extends MethodTest{
	
	private $objectProvider;
	
	public function __construct(IObjectProvider $objectProvider, $methodName, $methodArguments, $assertedResult){
		parent::__construct($methodName, $methodArguments, $assertedResult);
		$this->objectProvider = $objectProvider;
	}
	
	public function oneMoreSameMethod($methodArguments, $assertedResult){
		return new self($this->objectProvider, $this->methodName, $methodArguments, $assertedResult);
	}
	
	public function oneMoreSameObject($methodName, $methodArguments, $assertedResult){
		return new self($this->objectProvider, $methodName, $methodArguments, $assertedResult);
	}
			
	protected function action(){
		$object = $this->objectProvider->provideObject();
		$result = $object->{$this->methodName}(...$this->methodArguments);
		$this->checkResult($result);
	}

	
}