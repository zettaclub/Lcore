<?php 
namespace core\Lib\Tests;

use core\Lib\Test\ATestsBundle;
use core\Lib\Test\TestException;

class QArrayTest extends ATestsBundle{
	
	public function __construct(){
		parent::__construct("QArray library tests");
	}
	
	protected function buildTests(){
		$qArrayProvider = new \core\Lib\Test\ObjectProviders\FabricMethodObjectProvider("\core\Lib\Collections\QArray", "Q", [[1,2,3,4,5,6,7]]);
		$qTest = new \core\Lib\Test\DynamicMethodTest($qArrayProvider, "sum", [], 28);
		$this
			->pushTest($qTest)
			->pushTest($qTest->oneMoreSameObject("toArray", [], [1,2,3,4,5,6,7] ))
			->pushTest($qTest->oneMoreSameObject("remove", [1], function($res){ $this->compareQArrayToArray($res, [2,3,4,5,6,7]); }))
			->pushTest($qTest->oneMoreSameObject("remove", [function($p){return $p>4;}], function($res){ $this->compareQArrayToArray($res, [1,2,3,4,6,7]); }))
			->pushTest($qTest->oneMoreSameObject("removeAll", [[1,3,7]], function($res){ $this->compareQArrayToArray($res, [2,4,5,6]); }))
			->pushTest($qTest->oneMoreSameObject("removeAll", [function($p){return $p%2==1;}], function($res){ $this->compareQArrayToArray($res, [2,4,6]); }))
			->pushTest($qTest->oneMoreSameObject("map", [function($p){return $p*$p;}], function($res){ $this->compareQArrayToArray($res, [1,4,9,16,25,36,49]); }))
			->pushTest($qTest->oneMoreSameObject("where", [function($p){return $p>3;}], function($res){ $this->compareQArrayToArray($res, [4,5,6,7]); }))
			;
		$qArrayProvider = new \core\Lib\Test\ObjectProviders\FabricMethodObjectProvider("\core\Lib\Collections\QArray", "Q", [[1,5,2,7]]);
		$qTest = new \core\Lib\Test\DynamicMethodTest($qArrayProvider, "sorted", [], function($res){ $this->compareQArrayToArray($res, [1,2,5,7]); });
		$this
			->pushTest($qTest)
			->pushTest($qTest->oneMoreSameMethod([false], function($res){ $this->compareQArrayToArray($res, [7,5,2,1]); }))
			;
	}
	
	private function compareQArrayToArray($qArray, $array){
		if($qArray->toArray()!==$array){
			throw new TestException("\nWrong result: ".print_r($qArray->toArray(), true)."\n    expected: ".print_r($array, true));
		}
	}
	
}