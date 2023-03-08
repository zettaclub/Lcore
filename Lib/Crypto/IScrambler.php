<?php

namespace core\Lib\Crypto;

interface IScrambler{
	
	public function encrypt($value);
	public function decrypt($value);
	
}