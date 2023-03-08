<?php

namespace core\Lib\Crypto;

class FixedIVKeyScrambler implements IScrambler{
	
	private const CIPHER = "AES-256-CBC";
	
	#prop iv vGS
	private $iv;
	#prop key vGS
	private $key;
	
	public function __construct($key, $iv){
		$this->key = $key;
		$this->iv = $iv;
		if(strlen($iv)!==openssl_cipher_iv_length(self::CIPHER))
			throw new \Exception('Wrong IV length');
	}
	
	public function encrypt($value){
		return openssl_encrypt($value, self::CIPHER, $this->key, OPENSSL_RAW_DATA, $this->iv);
	}
	
	public function decrypt($value){
		return openssl_decrypt($value, self::CIPHER, $this->key, OPENSSL_RAW_DATA, $this->iv);
	}
	
	
	#gen - begin


	#gen - end
}