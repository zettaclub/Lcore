<?php

namespace core\Lib\Util;

class Random{
	
	public static $Alphabet62 = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
	
	public static function String($len, $alphabet = null){
		if($alphabet==null) $alphabet = self::$Alphabet62;
		$r = '';
		$ablen = strlen($alphabet);
		for ($i = 0; $i < $len; $i++)
			$r .= $alphabet[rand(0, $ablen-1)];
		return $r;
	}
	
	public static function Int($from, $to){
		return rand($from, $to);
	}
}