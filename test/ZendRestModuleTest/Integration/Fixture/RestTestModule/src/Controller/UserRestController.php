<?php


namespace Aeris\ZendRestModuleTest\RestTestModule\Controller;


use Doctrine\Common\Collections\ArrayCollection;
use Aeris\ZendRestModuleTest\RestTestModule\Model\User;
use Zend\Mvc\Controller\AbstractRestfulController;

class UserRestController extends AbstractRestfulController {

	public function get($id) {
		$user = new User();
		$user->id = 1;
		$user->name = 'jimmy';
		$user->phoneNumber = '555-1212';

		return $user;
	}

	public function getList() {
		$userA = new User();
		$userA->id = 1;
		$userA->name = 'jimmy';
		$userA->phoneNumber = '555-1212';

		$userB = new User();
		$userB->id = 2;
		$userB->name = 'sue';
		$userB->phoneNumber = '555-8989';

		return new ArrayCollection([
			$userA,
			$userB
		]);
	}

	public function getListAsArrayAction() {
		$userA = new User();
		$userA->id = 1;
		$userA->name = 'jimmy';
		$userA->phoneNumber = '555-1212';

		$userB = new User();
		$userB->id = 2;
		$userB->name = 'sue';
		$userB->phoneNumber = '555-8989';

		return [$userA, $userB];
	}

}
