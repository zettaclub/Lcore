<?php 

namespace core\Lib\Test\ObjectProviders;

class ConstructObjectProvider implements IObjectProvider{
	
	private $className;
	private $arguments;

	public function __construct($className, $arguments){
		$this->className = $className;
		$this->arguments = $arguments;
	}
	
	public function oneMore($arguments){
		return new self($this->className, $arguments);
	}
	
	public function provideObject(){
		return new ($this->className)(...$this->arguments);
	}
}