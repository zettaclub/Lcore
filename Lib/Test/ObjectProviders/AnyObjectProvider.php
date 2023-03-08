<?php 

namespace core\Lib\Test\ObjectProviders;

class AnyObjectProvider implements IObjectProvider{
	
	private $object;
	
	public function __construct($object){
		$this->object = $object;
	}
	
	public function provideObject(){
		return $this->object;
	}
}