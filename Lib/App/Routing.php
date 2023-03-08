<?php

namespace core\Lib\App;

use core\Lib\Controllers\AController;
use core\Lib\Log\InternalErrorsLog;
use core\Lib\Log\MysqlLog;
use core\Lib\Util\FileLocker;
use core\Lib\Views\HttpError;
use core\Lib\Views\Response;

class Routing{
	
	#prop rootRoutes vg tqarray
	private $rootRoutes;
	#prop errorPageRoute vg
	private $errorPageRoute;
	
	public function __construct($entry = 'def'){
		$file = App::I()->getProjectPath('/routing/'.$entry.'.routing.php');
		if(file_exists($file)) {
			$data = require $file;
			$this->rootRoutes = array_map(function($p){ return new Route($p); }, $data['routes']);
			if(isset($data['error-page'])){
				$this->errorPageRoute = new Route($data['error-page']);
			}
		}else throw new \Exception('Routing file not found');
	}
	
	public function route($request){
		$this->_route($request)->view();
	}
	
	public function _route($request){
		$match = null;
		try{
			foreach($this->rootRoutes as $sub){
				$match = $sub->route($request);
				if($match!==null) { 
					if($match->getController()!==null && $match->getAction()!==null){
						return AController::GetControllerView($request, $match);
					}
				}
			}
		}catch(Delegate $d){
			return $d->setRequest($request)->setRoute($match)->go();
		}
		catch(MysqlException $ex){
			MysqlLog::Log($ex);
			return $this->go500($request, $ex, $match);
			$response = Response::Go(500);
			if(App::I()->getModule('app')->getParam('mode')!=='prod'){
				$response->setContent(print_r($ex, true));
			}
			return $response;
		}catch(\Exception $ex){
			if($ex instanceof IView) return $ex;
			InternalErrorsLog::Log($ex);
			return $this->go500($request, $ex, $match);
			return Response::Go(500)->setContent(print_r($ex));
		}
		return Response::Go(404);
	}
	
	private function go500($request, $error, $route){
		if($route->getExpects()==='page' && $this->errorPageRoute!==null){
			$class = $this->errorPageRoute->getController();
			$controller = new $class;
			if($controller instanceof AController) $controller->beforeAction($request, $route);
			$result = $controller->{$this->errorPageRoute->getAction()."Action"}($request, $error);
			if($controller instanceof AController) $controller->afterAction($request);
			return $result;
		}
		$response = Response::Go(500);
		if(App::I()->getModule('app')->getParam('mode')!=='prod'){
			$response->setContent(print_r($error, true));
		}
		return $response;

	}
	
	#gen - begin
	public function getRootRoutes(){ return $this->rootRoutes; }
	protected function setRootRoutes($rootRoutes){ $this->rootRoutes = $rootRoutes; return $this; }
	public function getErrorPageRoute(){ return $this->errorPageRoute; }
	protected function setErrorPageRoute($errorPageRoute){ $this->errorPageRoute = $errorPageRoute; return $this; }
	#gen - end
}