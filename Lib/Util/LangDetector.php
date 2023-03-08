<?php

namespace core\Lib\Util;

class LangDetector{
	
	public static function Detect($request, $langs, $default){
		if($request->hasHeader("ACCEPT-LANGUAGE")){
			$val = $request->getHeader("ACCEPT-LANGUAGE");
			if (preg_match_all('/([a-z]{1,8}(?:-[a-z]{1,8})?)(?:;q=([0-9.]+))?/', $val, $list)) {
                $aLanguages = array_combine($list[1], $list[2]);
                foreach ($aLanguages as $n => $v)
                    $aLanguages[$n] = $v ? $v : 1;
                arsort($aLanguages, SORT_NUMERIC);
				
				$languages=array();
				foreach ($langs as $lang => $alias) {
					if (is_array($alias)) {
						foreach ($alias as $alias_lang) {
							$languages[strtolower($alias_lang)] = strtolower($lang);
						}
					}else $languages[strtolower($alias)]=strtolower($lang);
				}

				foreach ($aLanguages as $l => $v) {
					$s = strtok($l, '-');
					if (isset($languages[$s]))
						return $languages[$s];
				}
			}
		}
		return $default;
	}
	
}