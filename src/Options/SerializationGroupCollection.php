<?php


namespace Aeris\ZendRestModule\Options;


use Zend\Stdlib\AbstractOptions;

class SerializationGroupCollection extends AbstractOptions {

	/**
	 * Index by controller service name.
	 *
	 * @var SerializationGroups[]
	 */
	private $controllerGroups = [];

	public function __construct(array $options = []) {
		parent::__construct($this->normalizeOptions($options));
	}

	public static function deserialize($serialized) {
		$config = json_decode($serialized, true);
		return new self($config);
	}

	public function merge(SerializationGroupCollection $groupCollection) {
		/** @var SerializationGroups[] $controllerGroups */
		$controllerGroups = $groupCollection->getControllerGroups();

		foreach ($controllerGroups as $controller => $serializationGroup) {
			$actionGroups = $serializationGroup->getActionGroups();

			foreach ($actionGroups as $action => $groups) {
				$this->addGroups($groups, $controller, $action);
			}
		}
	}

	private function normalizeOptions(array $options) {
		$controllerGroups = [];
		foreach ($options as $controller => $groups) {
			$controllerGroups[$controller] = new SerializationGroups($groups);
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
			$this->controllerGroups[$controllerName] = new SerializationGroups();
		}

		$this->controllerGroups[$controllerName]->addGroupsForAction($actionName, $groups);
	}

	public function hasGroups($controllerName, $actionName) {
		return isset($this->controllerGroups[$controllerName]) &&
		$this->controllerGroups[$controllerName]->hasGroupsForAction($actionName);
	}

	public function getGroups($controllerName, $actionName) {
		return isset($this->controllerGroups[$controllerName]) ?
			$this
			->controllerGroups[$controllerName]
			->getGroupsForAction($actionName) : [];
	}

	public function serialize() {
		$config = [];

		foreach ($this->controllerGroups as $controller => $serializationGroup) {
			$config[$controller] = $serializationGroup->getActionGroups();
		}

		return json_encode($config);
	}

}