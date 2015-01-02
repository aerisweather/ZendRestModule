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
		foreach ($options as $controller => $groups) {
			$controllerGroups[$controller] = new SerializationGroup($groups);
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

	/**
	 * Set serialization groups for a controller's action.
	 *
	 * @param array $groups
	 * @param string $controllerName
	 * @param string $actionName
	 */
	public function addGroups(array $groups, $controllerName, $actionName) {
		if (!isset($this->controllerGroups[$controllerName])) {
			$this->controllerGroups[$controllerName] = new SerializationGroup();
		}

		$this->controllerGroups[$controllerName]->setGroupsForAction($actionName, $groups);
	}

	public function getGroups($controllerName, $actionName) {
		return $this
			->controllerGroups[$controllerName]
			->getGroupsForAction($actionName);
	}

}