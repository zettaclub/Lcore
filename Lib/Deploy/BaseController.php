<?php 
namespace core\Lib\Deploy;

use core\Lib\FileSystem\Path;
use core\Lib\Util\Random;

class BaseController{
	
	private static $confContent = '
<VirtualHost *:80>
    ServerAdmin admin@#host#
    ServerName #host#
    ServerAlias www.#host#
    DocumentRoot #web-dir#
	<Directory #web-dir#>
		Options -Indexes
		AllowOverride All
	</Directory>
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>';
	
	private static $htAccessContent = '
<IfModule mod_rewrite.c>
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule (.*) /entry/#entry#
</IfModule>';

	private static $entryContent = '
<?php require_once "#core-dir#/autoload.php";

core\Lib\App\App::I()
	->load("#project-dir#", "#workdir-dir#")
	->routeRequest(__DIR__."/../", "#routing#", "#host#");
';
	private static $runContent = '
<?php require_once "#core-dir#/autoload.php";

core\Lib\App\App::I()
	->load("#project-dir#", "#workdir-dir#")
	->runTask($argv);
';
	private static $hookContent = '
#!/bin/sh
git --work-tree=#project-path# --git-dir=#git-path# checkout -f';

	private function makeConfigContent($value){
		return "<?php return ".var_export($value, true).";";
	}
	
	private function createPathIfNotExists($path, $failMessage, $mode = 0755){
		if(!file_exists($path)){
			if(!mkdir($path, $mode, true)){
				exit($failMessage." ({$path})");
			}
			chmod($path, $mode);
		}
		return $this;
	}
		
	private function putFileIfNotExists($path, $content, $failMessage, $mode = 0644){
		if(!file_exists($path)){
			return $this->putFile($path, $content, $failMessage, $mode);
		}
		return $this;
	}
	
	private function putFile($path, $content, $failMessage, $mode = 0644){
		if(!file_put_contents($path, $content)){
			exit($failMessage." ({$path})");
		}
		chmod($path, $mode);
		return $this;
	}
	
	private function getTuning($coreDir){
		$tuningFile = Path::Combine($coreDir, "config", "tune.php");
		if(!file_exists($tuningFile)) exit("Core not tuned");
		return require $tuningFile;
	}
	
	private function validateProjectName($name){
		$name = trim($name);
		if(preg_match('/^[a-zA-Z0-9\-_]+$/', $name)===false)
			exit("Given project name is invalid: '{$name}'");
		return $name;
	}
		
	#tune-core <projects-path> <workdirs-path> <webs-path> [gits-path]
	public function tuneCoreAction($params, $coreDir){
		if(count($params)<3){
			exit("Not enough parameters");
		}
		$projectsPath = $params[0];
		$workdirsPath = $params[1];
		$websPath = $params[2];
		$gitsPath = isset($params[3])? $params[3]: "";
		$this
			->createPathIfNotExists($projectsPath, "Fail to create projects path")
			->createPathIfNotExists($workdirsPath, "Fail to create workdirs path")
			->createPathIfNotExists($websPath, "Fail to create webs path");
		if(strlen($gitsPath))
			$this->createPathIfNotExists($gitsPath, "Fail to create gits path");
		$tune = [
			"projects" => $projectsPath,
			"workdirs" => $workdirsPath,
			"webs" => $websPath,
			"gits" => $gitsPath
		];
		$this->putFile(Path::Combine($coreDir, "config", "tune.php"), 
				$this->makeConfigContent($tune), "Fail to write tuning file");
		echo "Tuned successfully\n";
	}
	
	#deploy-git project-name
	public function deployGitAction($params, $coreDir){
		$tuning = $this->getTuning($coreDir);
		$gitsPath = $tuning["gits"];
		if(strlen($gitsPath)===0) exit("Gits path not defined, please re-tune core");
		$projectsPath = $tuning["projects"];
		if(count($params)===0) exit("Project name not given");
		$projectName = $this->validateProjectName($params[0]);
		$gitPath = Path::Combine($gitsPath, $projectName.'.git');
		$projectPath = Path::Combine($projectsPath, $projectName);
		$this
			->createPathIfNotExists($gitPath, "Fail to create git-path")
			->createPathIfNotExists($projectPath, "Fail to create project-path");
		if(!file_exists(Path::Combine($gitPath, "index"))){
			if(exec("git --bare init \"{$gitPath}\"")===false)
				exit("Fail to init git");
		}
		$hookContent = str_replace(["#project-path#", "#git-path#"], [$projectPath, $gitPath], trim(self::$hookContent));
		$this->putFile(Path::Combine($gitPath, "hooks", "post-update"), $hookContent, "Fail to setup hook", 0755);
		$user = posix_getpwuid(posix_geteuid())["name"];
		echo "Success\n";
		echo "Git url: ssh://{$user}@[put_server_ip_here]{$gitPath}\next";
	}
	
	#deploy-project <project-name> [init]
	public function deployProjectAction($params, $coreDir){
		$tuning = $this->getTuning($coreDir);
		$projectsPath = $tuning["projects"];
		$workdirsPath = $tuning["workdirs"];
		$websPath = $tuning["webs"];

		if(count($params)===0) exit("Project name not given");
		$projectName = $this->validateProjectName($params[0]);

		$projectPath = Path::Combine($projectsPath, $projectName);
		$workdirPath = Path::Combine($workdirsPath, $projectName);
		$webPath = Path::Combine($websPath, $projectName);
		
		$runContent = str_replace(
				["#core-dir#", "#workdir-dir#", "#project-dir#"], 
				[$coreDir, $workdirPath, $projectPath], 
				trim(self::$runContent));

		
		$init = isset($params[1])? $params[1]=="init": false;
		$this
			->createPathIfNotExists($projectPath, "Fail to create project-path")
			->createPathIfNotExists($workdirPath, "Fail to create workdir-path");
		$consolePath = Path::Combine($workdirPath, "console");
		$this
			->createPathIfNotExists($consolePath, "Fail to create workdir console")
			->putFile(Path::Combine($consolePath, 'run.php'), $runContent, "Fail to create console run file");

		$source = Path::Combine($projectPath, "config");
		$target = Path::Combine($workdirPath, "config");
		$this
			->createPathIfNotExists($target, "Fail to create project configs path");
		
		$appWorkdirsPath = Path::Combine($projectPath, "config/workdirs.php");
		if(file_exists($appWorkdirsPath)){
			$appWorkdirsList = require $appWorkdirsPath;
			if(is_array($appWorkdirsList)){
				foreach($appWorkdirsList as $appWorkdir){
					$this
						->createPathIfNotExists(Path::Combine($workdirPath, $appWorkdir), "Fail to create app workdir path {$appWorkdir}", 0775);
				}
			}else echo "File config/workdirs.php has wrong format\n";
		} else echo "File config/workdirs.php not found\n";
		
		foreach(Path::ScanFiles($source, "*.config.php") as $file){
			if(!file_exists(Path::Combine($target, $file)))
				copy(Path::Combine($source, $file), Path::Combine($target, $file));
		}
		echo "Success\n";
	}
	
	public function deployWebAction($params, $coreDir){
		$tuning = $this->getTuning($coreDir);
		$projectsPath = $tuning["projects"];
		$workdirsPath = $tuning["workdirs"];
		$websPath = $tuning["webs"];
		if(count($params)===0) exit("Project name not given");
		$projectName = $this->validateProjectName($params[0]);
		$routing = "def";
		if(count($params)>1) $routing = $params[1];
		$host = null;
		if(count($params)>2) $host = $params[2];
		$projectPath = Path::Combine($projectsPath, $projectName);
		$workdirPath = Path::Combine($workdirsPath, $projectName);
		$webPath = Path::Combine($websPath, $projectName);

		$entryName = "entry".Random::String(12).".php";
		$htAccessContent = str_replace(["#entry#"], [$entryName], trim(self::$htAccessContent));
		$entryContent = str_replace(
				["#core-dir#", "#workdir-dir#", "#project-dir#", "#routing#"], 
				[$coreDir, $workdirPath, $projectPath, $routing], 
				trim(self::$entryContent));
		$this
			->createPathIfNotExists($webPath, "Fail to create web-path")	
			->createPathIfNotExists(Path::Combine($webPath, 'entry'), "Fail to create entry path")
			->putFile(Path::Combine($webPath, 'entry', $entryName), $entryContent, "Fail to create entry file")
			->putFile(Path::Combine($webPath, '.htaccess'), $htAccessContent, "Fail to create .htaccess file");

		foreach(Path::ScanFiles(Path::Combine($webPath, 'entry'), "*.php") as $file){
			if($file!==$entryName)
				unlink(Path::Combine($webPath, 'entry', $file));
		}

		if($host!==null){
			$confContent = str_replace(["#web-dir#", "#host#"], [$webPath, $host], trim(self::$confContent));
			$confName = $projectName."_".$routing.".conf";
			$this
				->putFile(Path::Combine("/etc/apache2/sites-available", $confName), $confContent, "Fail to create apache configuration file");
			if(exec("a2ensite {$confName}")===false){
				echo "Fail to enable host.\nPlease run:\n\tsudo a2ensite {$confName}\n";
			}
			if(exec("systemctl reload apache2")===false){
				echo "Please reload apache:\n\t systemctl reload apache2\n";
			}
		}	
		echo "Success\n";
	}
	
}