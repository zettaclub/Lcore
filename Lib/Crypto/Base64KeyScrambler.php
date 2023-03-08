<?php

namespace core\Lib\Crypto;

use core\Lib\Util\UrlSafeBase64;

class Base64KeyScrambler extends KeyScrambler{

	public function __construct($key){
		parent::__construct($key);
	}
	
	public function encrypt($value){
		return UrlSafeBase64::Base64Encode(parent::encrypt($value));
	}
	
	public function decrypt($value){
		return parent::decrypt(UrlSafeBase64::Base64Decode($value));
	}
	
	
}