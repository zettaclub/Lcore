<?php 
namespace core\Lib\Tests;

use core\Lib\Test\ATestsBundle;
use core\Lib\Test\TestException;

class PathTest extends ATestsBundle{
	
	public function __construct(){
		parent::__construct("Path library tests");
	}
	
	protected function buildTests(){
		$test = new \core\Lib\Test\StaticMethodTest("\core\Lib\FileSystem\Path", "Combine", ['/asd/root', '/bct'], '/asd/root/bct');
		$this
			->pushTest($test)
			->pushTest($test->oneMoreSameMethod(['/asd/root/', '/'], '/asd/root'))
			->pushTest($test->oneMoreSameMethod(['/asd/','/root', 'nessy'], '/asd/root/nessy'))
			->pushTest($test->oneMoreSameMethod(['bitch', 'nessy'], 'bitch/nessy'))
			->pushTest($test->oneMoreSameMethod(['/', '/'], '/'))
			->pushTest($test->oneMoreSameClass("ClearDirPath", ['/asd/root/'], '/asd/root'))
			->pushTest($test->oneMoreSameClass("ClearDirPath", ['/'], '/'))
			->pushTest($test->oneMoreSameClass("GetExtension", ['/adfh.ggh/files/file.sli'], 'sli'))
			->pushTest($test->oneMoreSameClass("GetExtension", ['/adfh.ggh/files/filesli'], null))
			->pushTest($test->oneMoreSameClass("GetExtension", ['/adfhggh/files/file.'], ""))
			->pushTest($test->oneMoreSameClass("GetExtension", ['/adfhggh/files/.sli'], "sli"))
			
			->pushTest($test->oneMoreSameClass("RemoveExtension", ['/adfhggh/files/f.sli'], "/adfhggh/files/f"))
			->pushTest($test->oneMoreSameClass("RemoveExtension", ['/adfhggh/files/sli'], "/adfhggh/files/sli"))
			
			->pushTest($test->oneMoreSameClass("GetDiff", ['/adfhggh/files/sli', '/adfhggh/files/'], "sli"))
			->pushTest($test->oneMoreSameClass("GetDiff", ['/adfhggh/files/sli', '/adfhggh/'], "files/sli"))

			->pushTest($test->oneMoreSameClass("GetParentPath", ['/adfhggh/files/.sli'], "/adfhggh/files"))
			->pushTest($test->oneMoreSameClass("GetParentPath", ['/adfhggh/files/'], "/adfhggh"))
			->pushTest($test->oneMoreSameClass("GetParentPath", ['/'], null))
			->pushTest($test->oneMoreSameClass("GetDirOrFileName", ['/adfhggh/files/tst.sli'], "tst.sli"))
			->pushTest($test->oneMoreSameClass("GetDirOrFileName", ['/adfhggh/files/'], "files"))
			->pushTest($test->oneMoreSameClass("GetDirOrFileName", ['/adfhggh/'], "adfhggh"))
			->pushTest($test->oneMoreSameClass("GetDirOrFileName", ['/'], "/"))

			->pushTest($test->oneMoreSameClass("IsRoot", ['/'], true))
			->pushTest($test->oneMoreSameClass("IsRoot", ['/abd/'], false))
			;

	}
	
}