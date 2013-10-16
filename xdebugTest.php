<?php
// time phpunit --coverage-html=./coverage xdebugTest.php
class xdebugTest extends PHPUnit_Framework_TestCase {
	public function testXDebug() {
		require_once(__DIR__. '/xdebug.php');
	}
}