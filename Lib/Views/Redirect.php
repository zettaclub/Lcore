<?php

namespace core\Lib\Views;

use core\Lib\App\IView;

class Redirect extends \Exception implements IView{
	
	#prop target vgS
	private $target;
	
	public function __construct($target){
		$this->target = $target;
	}
	
	public function view(){
		header("Location: {$this->target}");
	}
	
	#gen - begin
	public function getTarget(){ return $this->target; }

	#gen - end
}