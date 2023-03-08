<?php

namespace core\Lib\Views;

use core\Lib\App\IView;

class TinyFileView implements IView{
	#prop path vgs aprot
	protected $path;

	public function __construct($path){
		$this->path = $path;
	}
	
	public function view(){
		ob_clean();
		http_response_code(200);
		header('Content-Length: '.filesize($this->path));
		header('Content-Type: '.$this->getMimeType($this->path));
 		readfile($this->path);
	}
	
	public function getContent(){
		return file_get_contents($this->path);
	}
	
	private function getMimeType($path){
		if(preg_match("/\.svg$/i", $this->path)) return 'image/svg+xml';
		if(preg_match("/\.js$/i", $this->path)) return 'application/javascript';
		if(preg_match("/\.css$/i", $this->path)) return 'text/css';
		return mime_content_type($this->path);
	}


	#gen - begin
	public function getPath(){ return $this->path; }
	public function setPath($path){ $this->path = $path; return $this; }
	#gen - end
}