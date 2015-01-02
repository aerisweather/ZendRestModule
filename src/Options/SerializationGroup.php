<?php


namespace Aeris\ZendRestModule\Options;


use Zend\Stdlib\AbstractOptions;

class SerializationGroup extends AbstractOptions {

	/**
	 * Lists of groups, indexed by method name
	 *
	 * @var array[]
	 */
	private $methodGroups = [];

	public function __construct($options = []) {
		parent::__construct($this->normalizeOptions($options));
	}

	private function normalizeOptions(array $options) {
		$methodGroups = [];

		foreach ($options as $method => $groups) {
			$methodGroups[$method] = $groups;
		}

		return [
			'methodGroups' => $methodGroups,
		];
	}

	/**
	 * @return array
	 */
	public function getMethodGroups() {
		return $this->methodGroups;
	}

	/**
	 * @param array $methodGroups
	 */
	public function setMethodGroups($methodGroups) {
		$this->methodGroups = $methodGroups;
	}
}