<?php


namespace Aeris\ZendRestModule\Options;


use Zend\Stdlib\AbstractOptions;

class SerializationGroupCollection extends AbstractOptions {

	/**
	 * Index by controller service name.
	 *
	 * @var SerializationGroup[]
	 */
	private $controllerGroups = [];

	public function __construct($options = []) {
		parent::__construct($this->normalizeOptions($options));
	}

	private function normalizeOptions(array $options) {
		$controllerGroups = [];
		foreach ($options as $controller => $controllerGroups) {
			$controllerGroups[$controller] = new SerializationGroup($controllerGroups);
		}

		return [
			'controllerGroups' => $controllerGroups
		];
	}

	/**
	 * @return mixed
	 */
	public function getControllerGroups() {
		return $this->controllerGroups;
	}

	/**
	 * @param mixed $controllerGroups
	 */
	public function setControllerGroups($controllerGroups) {
		$this->controllerGroups = $controllerGroups;
	}

}