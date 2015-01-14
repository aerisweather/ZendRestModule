<?php


namespace Aeris\ZendRestModule\Options;


use Zend\Stdlib\AbstractOptions;

class SerializationGroups extends AbstractOptions {

	/**
	 * Lists of groups, indexed by action name
	 *
	 * @var array[]
	 */
	private $actionGroups = [];

	public function __construct($options = []) {
		parent::__construct($this->normalizeOptions($options));
	}

	private function normalizeOptions(array $options) {
		$groupsByAction = [];

		foreach ($options as $action => $groups) {
			$groupsByAction[$action] = $groups;
		}

		return [
			'actionGroups' => $groupsByAction,
		];
	}

	public function merge(SerializationGroups $serializationGroups) {
		foreach($serializationGroups->getActionGroups() as $action => $groups) {
			$this->addGroupsForAction($action, $groups);
		}
	}

	/**
	 * @return array
	 */
	public function getActionGroups() {
		return $this->actionGroups;
	}

	/**
	 * @param array $actionGroups
	 */
	public function setActionGroups(array $actionGroups) {
		$this->actionGroups = $actionGroups;
	}

	public function setGroupsForAction($action, array $groups) {
		$this->actionGroups[$action] = $groups;
	}

	public function addGroupsForAction($action, array $groups) {
		if (!isset($this->actionGroups[$action])) {
			$this->actionGroups[$action] = [];
		}

		$this->actionGroups[$action] = array_merge($this->actionGroups[$action], $groups);
	}

	public function getGroupsForAction($action) {
		return isset($this->actionGroups[$action]) ?
			$this->actionGroups[$action] : [];
	}

	public function hasGroupsForAction($action) {
		return isset($this->actionGroups[$action]);
	}

	public function toArray() {
		return $this->getActionGroups();
	}
}