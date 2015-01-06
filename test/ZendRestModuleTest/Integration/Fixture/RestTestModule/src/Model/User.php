<?php


namespace Aeris\ZendRestModuleTest\RestTestModule\Model;

use JMS\Serializer\Annotation as JMS;

/**
 * @JMS\ExclusionPolicy("all")
 */
class User {

	/**
	 * @JMS\Expose()
	 * @JMS\Groups({"userSummary", "userDetails"})
	 * @JMS\Type("string")
	 */
	public $id;

	/**
	 * @JMS\Expose()
	 * @JMS\Groups({"userSummary", "userDetails"})
	 * @JMS\Type("string")
	 */
	public $name;

	/**
	 * @JMS\Expose()
	 * @JMS\Groups({"userDetails"})
	 * @JMS\Type("string")
	 */
	public $phoneNumber;

	/**
	 * @JMS\Expose()
	 * @JMS\Type("Aeris\ZendRestModuleTest\RestTestModule\Model\User")
	 * @JMS\MaxDepth(1)
	 *
	 * @var User
	 */
	public $friend;

	/**
	 * @JMS\Expose()
	 * @JMS\Type("Aeris\ZendRestModuleTest\RestTestModule\Model\User")
	 * @JMS\MaxDepth(4)
	 *
	 * @var User
	 */
	public $enemy;

	public function __construct(array $props = []) {
		foreach ($props as $key => $val) {
			if (property_exists($this, $key)) {
				$this->$key = $val;
			}
		}
	}

}
