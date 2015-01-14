<?php


namespace Aeris\ZendRestModuleTest\RestTestModule\Model;

use JMS\Serializer\Annotation as JMS;


class BirthdayBoy {

	/**
	 * @JMS\Type("DateTimeTimestamp")
	 * @var \DateTime
	 */
	public $birthDate;

	/**
	 * @return \DateTime
	 */
	public function getBirthDate() {
		return $this->birthDate;
	}

	/**
	 * @param \DateTime $birthDate
	 */
	public function setBirthDate($birthDate) {
		$this->birthDate = $birthDate;
	}

}