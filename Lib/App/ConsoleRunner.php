<?php

namespace core\Lib\App;

use core\Lib\Util\FileLocker;

class ConsoleRunner{
	private $tasks;
	
	public function __construct(){
		if(!App::I()->hasModule("console")) throw new \Exception("Tasks not defined for current app");
		$this->tasks = App::I()->getModule("console");
	}
	
	public function run($taskName, $params){
		try{
			if(!$this->tasks->hasParam($taskName)) throw new \Exception("Task {$taskName} not defined for current app");
			$task = $this->tasks->getParam($taskName);
			$class = $task["controller"];
			$controller = new $class;
			if(isset($task["single-key"])){
				$key = $task["single-key"];
				FileLocker::Run($key, function()use($controller, $task, $params){
					$controller->{$task["action"]."Action"}($params);
				});
			}
			else $controller->{$task["action"]."Action"}($params);
		}catch(\Exception $ex){
			echo $ex->getMessage()."\n";
		}finally{
			echo "\n";
			//todo: close db connections, etc
		}
	}
	
}