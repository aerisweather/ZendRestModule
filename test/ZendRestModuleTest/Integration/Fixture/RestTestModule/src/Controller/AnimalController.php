<?php


namespace Aeris\ZendRestModuleTest\RestTestModule\Controller;


use Aeris\ZendRestModuleTest\RestTestModule\Exception\ForModifyingViewModelTestException;
use Zend\Mvc\Controller\AbstractActionController;

class AnimalController extends AbstractActionController {

	public function monkeyAction() {
		return ['monkey-see' => 'monkey-do'];
	}


	public function throwModifyViewModelExceptionAction() {
		throw new ForModifyingViewModelTestException();
	}

}
