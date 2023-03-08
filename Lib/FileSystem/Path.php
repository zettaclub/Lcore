<?php 

namespace core\Lib\FileSystem;

class Path{
	
	public static function Combine(...$parts){
		if(count($parts)===0) return '';
		$path = rtrim($parts[0], "\\/");
		for($i=1; $i<count($parts); $i++){
			$path .= DIRECTORY_SEPARATOR . trim($parts[$i], "\\/");
		}
		$path = rtrim($path, "\\/");
		return strlen($path)>0? $path: DIRECTORY_SEPARATOR;
	}
	
	public static function ClearDirPath($a){
		$path = rtrim($a, "\\/");
		return strlen($path)>0? $path: DIRECTORY_SEPARATOR;
	}
	
	public static function GetExtension($path){
		$lastSeparatorPos = strrpos($path, DIRECTORY_SEPARATOR);
		$dotPos = strrpos($path, '.');
		if($lastSeparatorPos>$dotPos) $dotPos = false;
		return $dotPos!==false? substr($path, $dotPos+1): null;
	}
	
	public static function RemoveExtension($path){
		$lastSeparatorPos = strrpos($path, DIRECTORY_SEPARATOR);
		$dotPos = strrpos($path, '.');
		if($lastSeparatorPos>$dotPos) $dotPos = false;
		return $dotPos!==false? substr($path, 0, $dotPos): $path;
	}
	
	public static function GetParentPath($path){
		$path = self::ClearDirPath($path);
		if(self::IsRoot($path)) return null;
		$lastSeparatorPos = strrpos($path, DIRECTORY_SEPARATOR);
		return ($lastSeparatorPos!==false && $lastSeparatorPos>0)? substr($path, 0, $lastSeparatorPos): null;
	}
	
	public static function GetDirOrFileName($path){
		$path = self::ClearDirPath($path);
		if(self::IsRoot($path)) return $path;
		$lastSeparatorPos = strrpos($path, DIRECTORY_SEPARATOR);
		return $lastSeparatorPos!==false? substr($path, $lastSeparatorPos+1): $path;
	}
	
	public static function GetClearFileName($path){
		return self::GetDirOrFileName(self::RemoveExtension($path));
	}
	
	public static function IsRoot($path){
		$path = self::ClearDirPath($path);
		return $path===DIRECTORY_SEPARATOR;
	}
	
	public static function Scan($path, $mask = null){
		$result = [];
		if($mask!==null) {
			$reg = "/".str_replace("\\*",".*", preg_quote($mask)).'/i';
			foreach(scandir($path) as $item){
				if($item==='.' || $item==='..') continue;
				if(preg_match($reg, $item)) $result[]= $item;
			}
		}else{
			foreach(scandir($path) as $item){
				if($item==='.' || $item==='..') continue;
				$result[]= $item;
			}
		}
		return $result;
	}
	
	public static function GetDiff($child, $parent){
		return trim(str_replace($parent, '', $child), "\\/");
	}
	
	public static function ScanFolders($path){
		$result = [];
		foreach(scandir($path) as $item){
			if($item==='.' || $item==='..') continue;
			if(is_file(self::Combine($path, $item))) continue;
			$result[]= $item;
		}
		return $result;
	}
	
	public static function ScanFiles($path, $mask = null){
		$result = [];
		if($mask!==null) {
			$reg = "/".str_replace("\\*",".*", preg_quote($mask)).'/i';
			foreach(scandir($path) as $item){
				if($item==='.' || $item==='..') continue;
				if(is_dir(self::Combine($path, $item))) continue;
				if(preg_match($reg, $item)) $result[]= $item;
			}
		}else{
			foreach(scandir($path) as $item){
				if($item==='.' || $item==='..') continue;
				if(is_dir(self::Combine($path, $item))) continue;
				$result[]= $item;
			}
		}
		return $result;
	}
	
	public static function ScanRecursive($path, $parent = null){
		$result = [];
		foreach(scandir($path) as $item){
			if($item==='.' || $item==='..') continue;
			$out = $parent!==null? self::Combine($parent, $item): $item;
			$full = self::Combine($path, $item);
			if(is_dir($full)){
				$list = self::ScanRecursive($full, $out);
				$result = array_merge($result, $list);
			}
			else $result[]= $out;
		}
		return $result;
	}
	
}