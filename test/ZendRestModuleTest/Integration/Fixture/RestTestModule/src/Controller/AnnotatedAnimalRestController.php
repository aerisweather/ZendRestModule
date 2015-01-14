<?php


namespace Aeris\ZendRestModuleTest\RestTestModule\Controller;

use Aeris\ZendRestModule\View\Annotation as View;
use Aeris\ZendRestModuleTest\RestTestModule\Model\Animal;
use Zend\Mvc\Controller\AbstractRestfulController;

class AnnotatedAnimalRestController extends AbstractRestfulController {

	/**
	 * @View\Groups({"animalDetails", "dates"})
	 * @param $id
	 */
	public function get($id) {
		return new Animal([
			'id' => $id,
			'species' => 'monkey',
			'color' => 'blue',
			'name' => 'Jojo',
			'birthDate' => 123456789
		]);
	}

	/**
	 * @View\Groups({"animalSummary"})
	 */
	public function getList() {
		return [
			new Animal([
				'id' => 1,
				'species' => 'monkey',
				'color' => 'blue',
				'name' => 'Jojo',
				'birthDate' => 123456789
			]),
			new Animal([
				'id' => 2,
				'species' => 'dinosaur',
				'color' => 'green',
				'name' => 'Yoshi',
				'birthDate' => 987654321
			]),
		];
	}

}