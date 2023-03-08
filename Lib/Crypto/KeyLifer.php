<?php

namespace core\Lib\Crypto;

use core\Lib\App\App;

class KeyLifer{
	
	#prop lifeKey vGS
	private $lifeKey;
	
	public function __construct(){
		$app = App::I();
		$module = $app->getModule('life');
		$this->lifeKey = $module->getParam('key');
	}
	
	public function mixKey($key){
		return md5($this->lifeKey.$key).sha1($key.$this->lifeKey);
	}
	
	public function mixKey2($key){
		$length = strlen($key);
		$j = 0;
		$list = [
				md5($this->lifeKey.($j++).$key, true),
				sha1($key.($j++).$this->lifeKey, true),
				sha1($this->lifeKey.($j++).$key, true),
				md5($key.($j++).$this->lifeKey, true),
			];
		$sec = implode('', $list);
		while(strlen($sec)<$length){
			$list[0] = $this->modify($j++, $list[0]);
			$list[1] = $this->modify($j++, $list[1]);
			$list[2] = $this->modify($j++, $list[2]);
			$list[3] = $this->modify($j++, $list[3]);
			$sec .= implode('', $list);
		}
		return substr($sec, 0, $length);
	}
	
	private function modify($j, $value){
		return strlen($value)===16
			? sha1($j.$value, true)
			: md5($value.$j, true);
	}
	
	public static function Get(){
		return new self();
	}
	
	
	#gen - begin


	#gen - end
}