<?php


namespace Aeris\ZendRestModuleTest\Integration\Annotations\Serializer;


use Aeris\ZendRestModuleTest\Integration\IntegrationTestCase;

class GroupAnnotationTest extends IntegrationTestCase {

	/** @test */
	public function shouldReturnAllPropertiesForTheConfiguredSerializationGroups() {
		$this->dispatch('/animals-annotated/1');
		$this->assertResponseStatusCode(200);

		$animal = $this->getJsonResponse();

		$this->assertArrayHasKey('id', $animal);
		$this->assertArrayHasKey('species', $animal);
		$this->assertArrayHasKey('color', $animal);
		$this->assertArrayHasKey('name', $animal);
		$this->assertArrayHasKey('birthDate', $animal);
	}

	/** @test */
	public function shouldNotReturnPropertiesExcludedFromASerializationGroup() {
		$this->dispatch('/animals-annotated');
		$this->assertResponseStatusCode(200);
		
		$animals = $this->getJsonResponse();
		$this->assertCount(2, $animals);
		
		$monkey = $animals[0];
		$dino = $animals[1];
		
		$this->assertArrayHasKey('id', $monkey);
		$this->assertArrayHasKey('name', $monkey);
		$this->assertArrayNotHasKey('species', $monkey);
		$this->assertArrayNotHasKey('color', $monkey);
		$this->assertArrayNotHasKey('birthDate', $monkey);
		
		$this->assertArrayHasKey('id', $dino);
		$this->assertArrayHasKey('name', $dino);
		$this->assertArrayNotHasKey('species', $dino);
		$this->assertArrayNotHasKey('color', $dino);
		$this->assertArrayNotHasKey('birthDate', $dino);
	}

}