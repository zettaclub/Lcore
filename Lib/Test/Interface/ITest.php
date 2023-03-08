<?php 

namespace core\Lib\Test\Interface;

interface ITest{
	public function run();
	public function isDone();
	public function isSuccess();
	public function getError();
}