<?php

namespace core\Lib\Views;

use core\Lib\App\IView;

class Response implements IView{
	
	#prop code vgs
	private $code;
	#prop headers vg
	private $headers = [];
	#prop content vgs
	private $content;
	
	public function __construct($code){
		$this->code = $code;
	}
	
	public static function Go($code = 200){
		return new self($code);
	}
	
	public static function GoContent($code = 200, $content = null){
		return self::Go($code)->setContent($content);
	}
	
	public static function PlainText($content){
		return self::Go()->setContent($content);
	}
	
	public static function Ajax($object){
		return self::Go()->setContent(json_encode($object));
	}
	
	public static function AjaxResult($object = null){
		return self::Go()->setContent(json_encode(['success' => true, 'result' => $object instanceof ICanToJSON? $object->toJSON(): $object]));
	}
	
	public static function AjaxError($error){
		return self::Go()->setContent(json_encode(['success' => false, 'error' => $error]));
	}
	
	public function view(){
		ob_clean();
		http_response_code($this->code);
		if(count($this->headers))
			foreach($this->headers as $header) header($header);
		echo $this->content;
	}
	
	public function addHeader($name, $value){
		$this->headers[$name] = "{$name}: {$value}";
		return $this;
	}
	
	public function removeHeader($name){
		if(isset($this->headers[$name])) unset($this->headers[$name]);
		return $this;
	}
	
	public function emptyHeaders(){
		return $this->setHeaders([]);
	}
	
	#gen - begin
	public function getCode(){ return $this->code; }
	public function setCode($code){ $this->code = $code; return $this; }
	public function getHeaders(){ return $this->headers; }
	protected function setHeaders($headers){ $this->headers = $headers; return $this; }
	public function getContent(){ return $this->content; }
	public function setContent($content){ $this->content = $content; return $this; }
	#gen - end
}