<?php

namespace core\Lib\App;

use core\Lib\FileSystem\Path;

class App{
	
	private static $instance;
	#prop modules vg
	private $modules = [];
	#prop coredir vg
	private $coredir;
	#prop projectdir vg
	private $projectdir;
	#prop workdir vg
	private $workdir;
	#prop webdir vg
	private $webdir;
	
	private function __construct(){}
	
	public static function I(){
		if(self::$instance===null) self::$instance = new self();
		return self::$instance;
	}
		
	public function load($projectdir, $workdir){
		$this->projectdir = $projectdir;
		$this->workdir = $workdir;
		$this->coredir = realpath(__DIR__."/../../");
		$defConfPath = $this->getProjectPath("config");
		foreach(Path::ScanFiles($defConfPath, "*.config.php") as $file){
			$this->modules[substr($file, 0, -11)] = new Module(require Path::Combine($defConfPath, $file));
		}
		$appConfPath = $this->getWorkdirPath("config");
		foreach(Path::ScanFiles($appConfPath, "*.config.php") as $file){
			$name = substr($file, 0, -11);
			if(isset($this->modules[$name])){
				$this->modules[$name]->accept(require Path::Combine($appConfPath, $file));
			}else{
				$this->modules[substr($file, 0, -11)] = new Module(require Path::Combine($appConfPath, $file));
			}
		}
		Autoloader::updateAutolader();
		return $this;
	}
	
	public function hasModule($name){
		return isset($this->modules[$name]);
	}
	
	public function getModule($name){
		return $this->modules[$name];
	}
	
	public function getProjectPath($relPath = ""){
		return Path::Combine($this->projectdir, $relPath);
	}
	
	public function getWorkdirPath($relPath = ""){
		return Path::Combine($this->workdir, $relPath);
	}
	
	public function getWebPath($relPath = ""){
		return Path::Combine($this->webdir, $relPath);
	}
	
	public function getCorePath($relPath = ""){
		return Path::Combine($this->coredir, $relPath);
	}
	
	public function routeRequest($webPath, $routing = 'def', $host = null){
		$this->webdir = $webPath;
		$routing = new Routing($routing);
		$routing->route(new Request());
	}
	
	public function runTask($argv){
		if(count($argv)<2) throw new \Exception("Task not given");
		$taskName = $argv[1];
		$consoleRunner = new ConsoleRunner();
		$consoleRunner->run($taskName, array_slice($argv, 2));
	}
	
	#gen - begin
	public function getModules(){ return $this->modules; }
	protected function setModules($modules){ $this->modules = $modules; return $this; }
	public function getCoredir(){ return $this->coredir; }
	protected function setCoredir($coredir){ $this->coredir = $coredir; return $this; }
	public function getProjectdir(){ return $this->projectdir; }
	protected function setProjectdir($projectdir){ $this->projectdir = $projectdir; return $this; }
	public function getWorkdir(){ return $this->workdir; }
	protected function setWorkdir($workdir){ $this->workdir = $workdir; return $this; }
	public function getWebdir(){ return $this->webdir; }
	protected function setWebdir($webdir){ $this->webdir = $webdir; return $this; }
	#gen - end
}