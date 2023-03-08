<?php

namespace core\Lib\ModelProto;

interface ICanToJSON{
	
	public function toJSON($rules = []);
	
}