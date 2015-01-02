<?php


namespace Aeris\ZendRestModule\Options;


use Zend\Stdlib\AbstractOptions;

class SerializationGroup extends AbstractOptions {

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

	/**
	 * @return array
	 */
	public function getActionGroups() {
		return $this->actionGroups;
	}

	/**
	 * @param array $actionGroups
	 */
	public function setActionGroups($actionGroups) {
		$this->actionGroups = $actionGroups;
	}

	public function setGroupsForAction($action, $groups) {
		$this->actionGroups[$action] = $groups;
	}

	public function getGroupsForAction($action) {
		return $this->actionGroups[$action];
	}

	public function hasGroupsForAction($action) {
		return isset($this->actionGroups[$action]);
	}
}