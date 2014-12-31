<?php


namespace Aeris\ZendRestModuleTest\Integration;


class TestedRestSegmentTest extends IntegrationTestCase {

	/** @test */
	public function shouldRouteToTheRestActionWhenTheTestPasses() {
		$this->dispatch('/animals/rest-tested/success/200', 'GET');

		$this->assertResponseStatusCode(200);
		$this->assertJsonResponseEquals(['foo' => 'bar']);
	}

	/** @test */
	public function should404IfTheTestFails() {
		$this->dispatch('/animals/rest-tested/fail/200', 'GET');

		$this->assertResponseStatusCode(404);
	}

}
