<?php


namespace Aeris\ZendRestModuleTest\Integration;

use \Mockery as M;
use Aeris\ZendRestModuleTest\RestTestModule\Exception\NotFoundException;
use Aeris\ZendRestModule\Event\RestErrorEvent;


class RestExceptionEventTest extends IntegrationTestCase {

	/** @test */
	public function shouldBeEmittedWithAControllerThrowsAnException() {
		$handlerMock = M::mock('stdClass');

		$handlerMock->shouldReceive('onError')
			->with(M::on(function ($evt) {
				/** @var RestErrorEvent $evt */
				$isRestErrorEvent = $evt instanceof RestErrorEvent;
				$hasNotFoundException = $evt->getError() instanceof NotFoundException;

				$errorConfig = $evt->getErrorConfig();
				$hasErrorConfig = $errorConfig['error'] === '\Aeris\ZendRestModuleTest\RestTestModule\Exception\NotFoundException' &&
					$errorConfig['http_code'] === 404 &&
					$errorConfig['application_code'] === 'not_found';

				return $isRestErrorEvent &&
				$hasNotFoundException &&
				$hasErrorConfig;
			}))
			->once();

		$sharedEventManager = $this->getApplication()
			->getEventManager()
			->getSharedManager();
		$sharedEventManager->attach(
			'Aeris\ZendRestModule\RestException',
			'exception',
			array($handlerMock, 'onError')
		);

		$this->dispatch('/animals/404', 'GET');
	}

	/** @test */
	public function shouldProvideAMechanismForAlteringTheErrorResponseObject() {
		$this->dispatch('/animals/alter-error-response-object', 'GET');

		$response = $this->getJsonResponse();
		$errorObj = $response['error'];

		$this->assertEquals('bar', $errorObj['foo']);
		$this->assertEquals(['barsy', 'shazlamy'], $response['foosies']);
	}

}
