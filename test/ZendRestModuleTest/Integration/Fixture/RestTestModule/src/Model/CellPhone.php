<?php


namespace Aeris\ZendRestModuleTest\RestTestModule\Model;

use JMS\Serializer\Annotation as JMS;


class CellPhone {

	/**
	 * A phone number, so Grandma can call us.
	 *
	 * @JMS\Type("PhoneNumber")
	 * @var string
	 */
	public $phoneNumber;

	/**
	 * @return string
	 */
	public function getPhoneNumber() {
		return $this->phoneNumber;
	}

	/**
	 * @param string $phoneNumber
	 * @return $this
	 */
	public function setPhoneNumber($phoneNumber) {
		$this->phoneNumber = $phoneNumber;
		return $this;
	}

}