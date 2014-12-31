<?php


namespace Aeris\ZendRestModuleTest\Integration;


class RestSegmentTest extends IntegrationTestCase {

	/** @test */
	public function get_shouldRouteToGetAction() {
		$this->dispatch('/animals/200');

		$this->assertResponseStatusCode(200);
		$this->assertJsonResponseEquals(['foo' => 'bar' ]);
	}

}
