<?php


namespace AAeris\ZendRestModuleTest\RestTestModule\Model;

use JMS\Serializer\Annotation as JMS;

class Animal {
	/**
	 * @JMS\Groups({"animalSummary", "animalDetails"})
	 * @var int
	 */
	public $id;

	/**
	 * @JMS\Groups({"animalDetails"})
	 * @var string
	 */
	public $species;

	/**
	 * @JMS\Groups({"animalDetails"})
	 * @var string
	 */
	public $color;

	/**
	 * @JMS\Groups({"animalSummary", "animalDetails"})
	 * @var string
	 */
	public $name;

	/**
	 * @JMS\Groups({"dates"})
	 * @var
	 */
	public $birthDate;

	public function __construct(array $props = []) {
		$this->hydrate($props);
	}

	/**
	 * @param array $props
	 */
	public function hydrate(array $props) {
		foreach ($props as $key => $val) {
			if (property_exists($this, $key)) {
				$this->$key = $val;
			}
		}
	}

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