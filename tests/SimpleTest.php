<?php
require_once('./required_functions.php');
Class SimpleTest extends PHPUnit_Framework_TestCase{
	public function testSetup() {
		$this->assertTrue(true);
	}

	public function testfoo() {

		$expected='Hello PHPUnit';

		$output = foo();

		$this->assertEquals($expected,$output);

		//$output = getAccountInformation('foo',3);

		//$this->assertEquals('expected output',$output);
	}
}