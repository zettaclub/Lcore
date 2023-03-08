<?php

namespace core\Lib\Controllers;

use core\Lib\Util\FileLocker;

abstract class AController{
	
	public function beforeAction($request, $route){}
	
	public function afterAction($request){}
	
	public static function GetControllerView($request, $route){
		if($route->hasSingleKey()){
			return FileLocker::Run($route->getSingleKey($request), function()use($request, $route){
				return AController::_getControllerView($request, $route);
			}, new HttpError(409));
		}else{
			return self::_getControllerView($request, $route);
		}
	}
	
	private static function _getControllerView($request, $route){
		$class = $route->getController();
		$controller = new $class;
		if($controller instanceof AController) $controller->beforeAction($request, $route);
		$result = $controller->{$route->getAction()."Action"}($request);
		if($controller instanceof AController) $controller->afterAction($request);
		return $result;
	}
	
}