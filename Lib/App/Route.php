<?php

namespace core\Lib\App;

use core\Lib\Collections\QArray;
use core\Lib\Views\HttpError;

class Route{
	
	#prop path vg
	private $path;
	#prop children vgS
	private $children;
	#prop controller vg
	private $controller;
	#prop action vg
	private $action;
	#prop method vg
	private $method;
	#prop isReg vg
	private $isReg = false;
	#prop regex vg
	private $regex;
	#prop paramNames vg
	private $paramNames = [];
	#prop expects vg
	private $expects;
	#prop singleKey vGS
	private $singleKey;
	
	public function __construct($info){
		$this->path = isset($info['path'])? $info['path']: '';
		$this->controller = isset($info['controller'])? $info['controller']: null;
		$this->action = isset($info['action'])? $info['action']: null;
		$this->method = isset($info['method'])? strtoupper($info['method']): 'GET';
		$this->expects = isset($info['expects'])? strtolower($info['expects']): 'json';
		$this->singleKey = isset($info['single'])? strtolower($info['single']): null;
		$this->children = isset($info['sub'])? array_map(function($p){ return new self($p); }, $info['sub']): [];
		#var_dump($this->path);
		if(preg_match("/\\[[^\\]]+\\]/i", $this->path)){
			#echo " ".htmlspecialchars($this->path);
			$this->regex = "";
			$this->isReg = true;
			$split = preg_split("/(\\[[^\\]]+\\])/i", $this->path, -1, PREG_SPLIT_DELIM_CAPTURE);
			foreach($split as $item){
				#echo " ".htmlspecialchars($item);
				if(preg_match("/\\[(\\w+)(?:<([^>]+)>)?\\]/i", $item, $match)){
					//var_dump($match);
					#echo htmlspecialchars("(?<".$match[1].">".$match[2].")");
					#var_dump("(?<".$match[1].">".$match[2].")");
					$this->regex .= "(?<".$match[1].">".(isset($match[2])? $match[2]: "\\w+").")";
					$this->paramNames[]= $match[1];
				}else{
					$this->regex .= preg_quote($item);
				}
			}
			$this->regex = '/^'.str_replace('/', '\\/', $this->regex).'/';
			#echo " ".htmlspecialchars($this->regex);
		}
	}
	
	public function route($request, $least = null, $params = []){
		return $this->isReg
				? $this->regexpRouting($request, $least, $params)
				: $this->simpleRoute($request, $least, $params);
	}
	
	public function regexpRouting($request, $least = null, $params = []){
		$subParams = $params;
		if($least===null) $least = $request->getUrl();
		if(preg_match($this->regex, $least, $match)){
			foreach($this->paramNames as $name){
				$subParams[$name] = $match[$name];
			}
			$least = preg_replace($this->regex, '', $least);
			if(strlen($least)>0){
				foreach($this->children as $sub){
					$match = $sub->route($request, $least, $subParams);
					if($match!==null) return $match;
				}
			}else{
				if($request->getMethod()==$this->method){
					$request->setUrlParams($subParams);
					return $this;
				}
			}
		}
		return null;
	}
	
	protected function simpleRoute($request, $least = null, $params = []){
		if($least===null) $least = $request->getUrl();
		$len = strlen($this->path);
		#var_dump($least);
		if(substr($least, 0, $len)==$this->path){
			$least = substr($least, $len);
			if(strlen($least)>0){
				foreach($this->children as $sub){
					$match = $sub->route($request, $least, $params);
					if($match!==null) return $match;
				}
			}else{
				if($request->getMethod()==$this->method){
					$request->setUrlParams($params);
					return $this;
				}
			}
		}
		return null;
	}
	
	public function hasSingleKey(){ return $this->singleKey!==null; }
	
	public function getSingleKey($request){ 
		$result = preg_replace_callback("/#(r|u)\.(\w+)/i", function($match)use($request){
			if($match[1]=='u') return $request->urlParam($match[2]);
			if($match[1]=='r') return $request->reqParam($match[2]);
			throw new HttpError(500);
		}, $this->singleKey);
		if(!preg_match('/^[\w\-]+$/i', $result))
			throw new HttpError(500);
		return $result;
	}
	
	#gen - begin
	public function getPath(){ return $this->path; }
	protected function setPath($path){ $this->path = $path; return $this; }
	public function getChildren(){ return $this->children; }
	public function getController(){ return $this->controller; }
	protected function setController($controller){ $this->controller = $controller; return $this; }
	public function getAction(){ return $this->action; }
	protected function setAction($action){ $this->action = $action; return $this; }
	public function getMethod(){ return $this->method; }
	protected function setMethod($method){ $this->method = $method; return $this; }
	public function getIsReg(){ return $this->isReg; }
	protected function setIsReg($isReg){ $this->isReg = $isReg; return $this; }
	public function getRegex(){ return $this->regex; }
	protected function setRegex($regex){ $this->regex = $regex; return $this; }
	public function getParamNames(){ return $this->paramNames; }
	protected function setParamNames($paramNames){ $this->paramNames = $paramNames; return $this; }
	public function getExpects(){ return $this->expects; }
	protected function setExpects($expects){ $this->expects = $expects; return $this; }

	#gen - end
}