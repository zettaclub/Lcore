<?php

namespace core\Lib\App;

class Delegate extends \Exception{
	
	#prop controller vgs
	private $controller;
	#prop action vgs
	private $action;
	#prop request vgs
	private $request;
	#prop route vgs
	private $route;
	
	public function __construct($controller, $action){
		$this->controller = $controller;
		$this->action = $action;
	}
	
	public function go(){
		try{
			$class = $this->controller;
			$controller = new $class;
			if($controller instanceof AController) $controller->beforeAction($this->request, $this->route);
			$result = $controller->{$this->action."Action"}($this->request);
			if($controller instanceof AController) $controller->afterAction($this->request);
			return $result;
		}catch(Delegate $ex){
			return $ex->setRequest($this->request)->setRoute($this->route)->go();
		}
	}
		
	#gen - begin
	public function getController(){ return $this->controller; }
	public function setController($controller){ $this->controller = $controller; return $this; }
	public function getAction(){ return $this->action; }
	public function setAction($action){ $this->action = $action; return $this; }
	public function getRequest(){ return $this->request; }
	public function setRequest($request){ $this->request = $request; return $this; }
	public function getRoute(){ return $this->route; }
	public function setRoute($route){ $this->route = $route; return $this; }
	#gen - end
}