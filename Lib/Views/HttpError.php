<?php

namespace core\Lib\Views;

use \Exception;
use core\Lib\App\IView;

class HttpError extends Exception implements IView{
	
	#prop httpCode vgs
	private $httpCode;

	public function __construct($httpCode){
		$this->httpCode = $httpCode;
	}
	
	public function view(){
		ob_clean();
		http_response_code($this->httpCode);
	}
	#gen - begin
	public function getHttpCode(){ return $this->httpCode; }
	public function setHttpCode($httpCode){ $this->httpCode = $httpCode; return $this; }
	#gen - end
}