<?php 

namespace core\Lib\Test\ObjectProviders;

class FabricMethodObjectProvider implements IObjectProvider{
	
	private $className;
	private $methodName;
	private $arguments;

	public function __construct($className, $methodName, $arguments = []){
		$this->className = $className;
		$this->methodName = $methodName;
		$this->arguments = $arguments;
	}
	
	public function oneMore($arguments){
		return new self($this->className, $this->methodName, $arguments);
	}
	
	public function provideObject(){
		return ($this->className)::{$this->methodName}(...$this->arguments);
	}
}