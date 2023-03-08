<?php

namespace core\Lib\Controllers;

use core\Lib\App\App;
use core\Lib\FileSystem\Path;
use core\Lib\Views\CssPacker;
use core\Lib\Views\HttpError;
use core\Lib\Views\Response;
use core\Lib\Views\TinyFileView;

class DefaultAssetsController extends AController{
	
	private $fileView = null;
	private $inWebPath = null;
	
	public function getAssetAction($request){
		$projectAssetsPath = realpath(App::I()->getProjectPath('assets'));
		$assetRelPath = $request->urlParam('path');
		$assetPath = realpath(Path::Combine($projectAssetsPath, $assetRelPath));
		if($assetPath===false) throw new HttpError(404);
		if(strpos($assetPath, $projectAssetsPath)!==0) throw new HttpError(403);
		if(App::I()->getModule('app')->getParam('mode')==='prod'){
			$this->inWebPath = App::I()->getWebPath(Path::Combine("assets", $assetRelPath));
		}
		if(preg_match("/\.css$/i", $assetPath)) $this->fileView = new CssPacker($assetPath);
		else $this->fileView = new TinyFileView($assetPath);
		return $this->fileView;
	}
	
	public function afterAction($request){
		if($this->inWebPath === null) return;
		if(!file_exists($this->inWebPath)){
			$dirPath = Path::GetParentPath($this->inWebPath);
			if(!file_exists($dirPath)){
				mkdir($dirPath, 0755, true);
			}
			file_put_contents($this->inWebPath, $this->fileView->getContent());
		}
	}
	
}