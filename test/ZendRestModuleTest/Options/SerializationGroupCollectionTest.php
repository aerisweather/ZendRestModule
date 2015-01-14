<?php


namespace Aeris\ZendRestModuleTest\Options;


use Aeris\ZendRestModule\Options\SerializationGroupCollection;

class SerializationGroupCollectionTest extends \PHPUnit_Framework_TestCase {

	/** @test */
	public function serialize_deserialize_shouldCreateIdenticalOptionsObjects() {
		$serializationGroups = new SerializationGroupCollection([
			'MyApp\Controller\UserRest' => [
				'get' => ['details', 'dates'],
				'getList' => ['summary']
			],
			'MyApp\Controller\AnimalRest' => [
				'monkeyAction' => ['monkeySee', 'monkeyDo']
			]
		]);

		$reserialized = SerializationGroupCollection::deserialize($serializationGroups->serialize());

		$this->assertEquals(
			['details', 'dates'],
			$reserialized->getGroups('MyApp\Controller\UserRest', 'get')
		);
		$this->assertEquals(
			['summary'],
			$reserialized->getGroups('MyApp\Controller\UserRest', 'getList')
		);
		$this->assertEquals(
			['monkeySee', 'monkeyDo'],
			$reserialized->getGroups('MyApp\Controller\AnimalRest', 'monkeyAction')
		);

		$this->assertCount(2, $reserialized->getControllerGroups());
	}

}