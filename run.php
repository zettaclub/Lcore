<?php require_once __DIR__.DIRECTORY_SEPARATOR."autoload.php";
$operations = require __DIR__.DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR."operations.php";
if(count($argv)<2){
	echo "Operation not given\n";
	exit;
}
$operation = $argv[1];
if(!isset($operations[$operation])){
	echo "Unknown operation\n";
	exit;
}
$info = $operations[$operation];
$class = $info["class"];
$controller = new $class;
$controller->{$info["action"]."Action"}(array_slice($argv, 2), __DIR__);