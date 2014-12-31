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

}
