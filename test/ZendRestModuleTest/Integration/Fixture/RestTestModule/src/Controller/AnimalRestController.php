<?php


namespace Aeris\ZendRestModuleTest\RestTestModule\Controller;


use Aeris\ZendRestModuleTest\RestTestModule\Exception\NotFoundException;
use Zend\Mvc\Controller\AbstractRestfulController;

class AnimalRestController extends AbstractRestfulController {

	public function get($id) {

		if ((int) $id === 404) {
			throw new NotFoundException();
		}

		return [
			'foo' => 'bar'
		];
	}

}
