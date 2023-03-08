<?php

namespace core\Lib\Crypto;

class KeyScrambler implements IScrambler{
	
	private const CIPHER = "AES-256-CBC";
	
	#prop key vGS
	private $key; 
	#prop ivlen vGS
	private $ivlen;

	public function __construct($key){
		$this->key = $key;
		$this->ivlen = openssl_cipher_iv_length(self::CIPHER);
	}
	
	public function encrypt($value){
		$iv = openssl_random_pseudo_bytes($this->ivlen);
		$encrypted = openssl_encrypt($value, self::CIPHER, $this->key, OPENSSL_RAW_DATA, $iv);
		return $iv.$encrypted;
	}
	
	public function decrypt($value){
		$iv = substr($value, 0, $this->ivlen);
		$encrypted = substr($value, $this->ivlen);
		return openssl_decrypt($encrypted, self::CIPHER, $this->key, OPENSSL_RAW_DATA, $iv);
	}
	
	
	#gen - begin


	#gen - end
}