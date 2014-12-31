<?php


namespace Aeris\ZendRestModuleTest\Integration;


class TestedLiteralTest extends IntegrationTestCase {

	/** @test */
	public function shouldRouteToTheRestActionWhenTheTestPasses() {
		$this->dispatch('/animals/literal-tested/success', 'GET');

		$this->assertResponseStatusCode(200);
		$this->assertJsonResponseEquals(['monkey-see' => 'monkey-do']);
	}

	/** @test */
	public function should404IfTheTestFails() {
		$this->dispatch('/animals/literal-tested/fail', 'GET');

		$this->assertResponseStatusCode(404);
	}

}
