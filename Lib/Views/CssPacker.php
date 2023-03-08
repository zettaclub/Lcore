<?php

namespace core\Lib\Views;

use core\Lib\FileSystem\Path;

class CssPacker extends TinyFileView{
	
	private $content;
	
	public function view(){
		ob_clean();
		http_response_code(200);
		header('Content-Length: '.strlen($this->getContent()));
		header("Content-Type: text/css");
 		echo $this->getContent();
	}

	public function getContent(){
		if($this->content===null){
			$this->content = self::process($this->path);
		}
		return $this->content;
	}
	
	private static function process($path){
		return preg_replace_callback('/@import url\(("[^"]+"|\'[^\']+\')\);/i', function($match)use($path){
			$url = trim($match[1], "\"'");
			if(preg_match('/^\.\//i', $url)){
				$sub = realpath(Path::Combine(Path::GetParentPath($path), substr($url, 1)));
				if($sub!==false) return self::process($sub);
			}
			if(preg_match('/^\.\.\//i', $url)){
				$sub = realpath(Path::Combine(Path::GetParentPath($path), $url));
				if($sub!==false) return self::process($sub);
			}
			if(preg_match('/^\//i', $url)){
				$sub = realpath(App::I()->getProjectPath($url));
				if($sub!==false) return self::process($sub);
			}
			return $match[0];
		}, file_get_contents($path));
	}
	
}