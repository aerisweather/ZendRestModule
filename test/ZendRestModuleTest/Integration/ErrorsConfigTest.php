<?php


namespace Aeris\ZendRestModuleTest\Integration;


use Aeris\ZendRestModule\Event\RestErrorEvent;
use \Mockery as M;

class ErrorsConfigTest extends IntegrationTestCase {


	/**
	 * @test
	 */
	public function shouldInvokeOnErrorCallbackWithErrorEvent() {
		$this->dispatch('/animals/404', 'GET');

		$this->assertTrue($GLOBALS['zend-rest-test-errors-not-found-onerror-was-called']);

		/** @var RestErrorEvent $evt */
		$evt = $GLOBALS['zend-rest-errors-not-found-on-error-args'][0];
		$this->assertInstanceOf('Aeris\ZendRestModule\Event\RestErrorEvent', $evt);

		$error = $evt->getError();
		$errorConfig = $evt->getErrorConfig();

		$this->assertInstanceOf('Aeris\ZendRestModuleTest\RestTestModule\Exception\NotFoundException', $error);
		$this->assertEquals(404, $errorConfig['httpCode']);
		$this->assertEquals('not_found', $errorConfig['applicationCode']);
	}

	/** @test */
	public function shouldAcceptADetailsConfigCallback() {
		$this->dispatch('/animals/404', 'GET');

		$response = json_decode($this->getResponse()->getContent(), true);
		$this->assertEquals('foo', $response['error']['details']);
	}

}
