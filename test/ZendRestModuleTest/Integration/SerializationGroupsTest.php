<?php


namespace Aeris\ZendRestModuleTest\Integration;


class SerializationGroupsTest extends IntegrationTestCase {

	/** @test */
	public function shouldReturnAllPropertiesForTheConfiguredSerializationGroup() {
		$this->dispatch('/users/1');

		$this->assertResponseStatusCode(200);

		$json = $this->getJsonResponse();
		$this->assertEquals('jimmy', $json['name']);
		$this->assertEquals('555-1212', $json['phoneNumber']);
	}

	/** @test */
	public function shouldNotReturnPropertiesExcludedFromASerializationGroup() {
		$this->dispatch('/users');

		$this->assertResponseStatusCode(200);

		$json = $this->getJsonResponse();
		$userA = $json[0];

		$this->assertEquals('jimmy', $userA['name']);
		$this->assertArrayNotHasKey('phoneNumber', $userA);
	}

}
