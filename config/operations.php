<?php 
return [
	"tune-core" =>[
		"class" => \core\Lib\Deploy\BaseController::class,
		"action" => "tuneCore"
	],
	"deploy-git" =>[
		"class" => \core\Lib\Deploy\BaseController::class,
		"action" => "deployGit"
	],
	"deploy-project" =>[
		"class" => \core\Lib\Deploy\BaseController::class,
		"action" => "deployProject"
	],
	"deploy-web" =>[
		"class" => \core\Lib\Deploy\BaseController::class,
		"action" => "deployWeb"
	]
];