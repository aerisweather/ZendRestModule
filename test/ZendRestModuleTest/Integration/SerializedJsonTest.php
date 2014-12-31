<?php


namespace Aeris\ZendRestModuleTest\Integration;


class SerializedJsonTest extends IntegrationTestCase {

	/** @test */
	public function shouldSerializeArraysOfObjects() {
		$this->dispatch('/users/as-array', 'GET');

		// we had a bug where json was getting arrays
		// encoded as objects (~ { "0" : {}, "1": {} )
		$jsonStringResponse = $this->getResponse()->getContent();
		$this->assertJsonStringEqualsJsonString(
			'[{"id":"1","name":"jimmy","phoneNumber":"555-1212"},{"id":"2","name":"sue","phoneNumber":"555-8989"}]',
			$jsonStringResponse
		);
	}

}
