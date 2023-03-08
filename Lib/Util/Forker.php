<?php

namespace core\Lib\Util;

class Forker{
	
	private $children = [];
	
	public function fork($callable, $runSynchroniusOnForkFail = false){
		$pid = pcntl_fork();
		if ($pid == -1) {
			if($runSynchroniusOnForkFail){
				$callable();
			}else{
				throw new \Exception("Fail to fork process");
			}
		} else if ($pid) {
			$this->children[] = $pid;
			return $this;
		} else {
			$callable();
			die();
		}
	}
	
	public function waitChildren(){
		foreach($this->children as $pid)
			pcntl_waitpid($pid, $status);
	}
	
}