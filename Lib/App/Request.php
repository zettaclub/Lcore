<?php

namespace core\Lib\App;

class Request{
	
	#prop method vgS
	private $method;
	#prop queryString vgS
	private $queryString;
	#prop url vgS
	private $url;
	#prop headers vgS
	private $headers;
	#prop cookies vgS
	private $cookies;
	#prop getParams vgS
	private $getParams = [];
	#prop postParams vgS
	private $postParams = [];
	#prop urlParams vgs
	private $urlParams = [];
	private $session = null;
	
	public function __construct(){
		list($this->url, $this->queryString) = 
			strpos($_SERVER['REQUEST_URI'], '?')!==false
				? explode('?', $_SERVER['REQUEST_URI'], 2)
				: [$_SERVER['REQUEST_URI'], null];
		$this->url = rtrim($this->url, '/');
		$this->url = preg_replace("/\\/{2,}/i", '/', $this->url);
		$this->method = strtoupper($_SERVER['REQUEST_METHOD']);
		if($this->method==='POST') $this->postParams = $_POST;
		$this->headers = $this->requestHeaders();
		$this->cookies = $_COOKIE;
		if($this->queryString!==null && strpos($this->queryString, '=')!==false){
			foreach(explode('&', $this->queryString) as $part){
				list($name, $value) = explode('=', $part, 2);
				$this->getParams[$name] = urldecode($value);
			}
		}else $this->queryString = '';
	}
	
	public function isPost(){ return $this->method==='POST'; }
	public function isGet(){ return $this->method==='GET'; }
	
	public function hasHeader($name){ return isset($this->headers[$name]); }
	public function getHeader($name){ return $this->headers[$name]; }
	
	public function hasCookie($name){ return isset($this->cookies[$name]); }
	public function getCookie($name){ return $this->cookies[$name]; }
	public function setCookie($name, $value, $options = []){
		$expiers = isset($options["expires"])? $options["expires"]: 0;
		$path = isset($options["path"])? $options["path"]: "/";
		$secure = isset($options["secure"])? $options["secure"]: false;
		$httponly = isset($options["httponly"])? $options["httponly"]: false;
		setcookie($name, $value, $expiers, $path, "", $secure, $httponly);
		return $this;
	}
	
	public function unsetCookie($name){
		return $this->setCookie($name, "", ["expires" => time()-24*3600]);
	}
	
	public function getClientIp(){
		$value = '';
		if (!empty($this->headers['CLIENT-IP'])) {
			$value = $this->headers['CLIENT-IP'];
		} elseif (!empty($this->headers['X-FORWARDED-FOR'])) {
			$value = $this->headers['X-FORWARDED-FOR'];
		} elseif (!empty($_SERVER['REMOTE_ADDR'])) {
			$value = $_SERVER['REMOTE_ADDR'];
		}
		return $value;
	}
	
	public function postParam($name){
		return $this->reqParam($name);
	}

	public function getParam($name){
		return $this->reqParam($name);
	}
	
	public function reqParam($name){
		if($this->method==='POST') return $this->postParams[$name];
		return $this->getParams[$name];
	}
	
	public function reqParamOrDef($name, $default = null){
		if($this->method==='POST') return isset($this->postParams[$name])? $this->postParams[$name]: $default;
		return isset($this->getParams[$name])? $this->getParams[$name]: $default;
	}

	public function urlParam($name){
		return $this->urlParams[$name];
	}

	public function getSession(){
		if($this->session===null) $this->session = new Session();
		return $this->session;
	}
	
	private function requestHeaders(){
		if(function_exists('apache_request_headers')) return array_change_key_case(apache_request_headers(), CASE_UPPER);
		$arh = array();
		$rx_http = '/\AHTTP_/';
		foreach($_SERVER as $key => $val){
			if(preg_match($rx_http, $key)){
				$arh_key = preg_replace($rx_http, '', $key);
				$rx_matches = [];
				$rx_matches = explode('_', $arh_key);
				if(count($rx_matches)>0 and strlen($arh_key)>2){
        			foreach($rx_matches as $ak_key => $ak_val) $rx_matches[$ak_key] = ucfirst($ak_val);
       				$arh_key = implode('-', $rx_matches);
				}
				$arh[$arh_key] = $val;
			}
		}
		return $arh;
	}
	
	#gen - begin
	public function getMethod(){ return $this->method; }
	public function getQueryString(){ return $this->queryString; }
	public function getUrl(){ return $this->url; }
	public function getHeaders(){ return $this->headers; }
	public function getCookies(){ return $this->cookies; }
	public function getGetParams(){ return $this->getParams; }
	public function getPostParams(){ return $this->postParams; }
	public function getUrlParams(){ return $this->urlParams; }
	public function setUrlParams($urlParams){ $this->urlParams = $urlParams; return $this; }
	#gen - end
}