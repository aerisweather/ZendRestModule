<?php


namespace Aeris\ZendRestModuleTest\Integration;


class RestExceptionTest extends IntegrationTestCase {

	/** @test */
	public function shouldReturnJsonErrorObjects() {
		$this->dispatch('/not/an/endpoint', 'GET');

		$response = $this->getJsonResponse();
		$this->assertArrayHasKey('error', $response);

		$errorObj = $response['error'];
		$this->assertEquals('invalid_request', $errorObj['code']);
		$this->assertEquals('The requested endpoint or action is invalid and not supported.', $errorObj['details']);

		$this->assertResponseStatusCode(404);
	}

}
