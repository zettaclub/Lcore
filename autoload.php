<?php
function __base_autoload($class) {
	$file = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.str_replace('\\', DIRECTORY_SEPARATOR, $class).'.php';
	if (file_exists($file)) require_once $file;
	else throw new \Exception("Class {$class} not found in {$file}");
}
spl_autoload_register('__base_autoload');