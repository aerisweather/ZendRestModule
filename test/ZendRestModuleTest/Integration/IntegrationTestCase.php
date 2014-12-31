<?php


namespace Aeris\ZendRestModuleTest\Integration;


use Aeris\ZendRestModuleTest\AbstractTestCase;

class IntegrationTestCase extends AbstractTestCase {



	public function __construct() {
		parent::__construct();

		$this->appConfigDir = __DIR__ . '/Fixture/config';
	}

	public function tearDown(){
		unset($GLOBALS['zend-rest-test-errors-not-found-onerror-was-called']);
		unset($GLOBALS['zend-rest-errors-not-found-on-error-args']);
	}

}
