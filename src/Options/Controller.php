<?php


namespace Aeris\ZendRestModule\Options;


use Zend\Stdlib\AbstractOptions;

class Controller extends AbstractOptions {

	/** @var SerializationGroups */
	private $serializationGroups;

	public function __construct(array $options = []) {
		$defaults = [
			'serialization_groups' => []
		];

		parent::__construct(array_replace($defaults, $options));
	}

	/**
	 * @param array|Controller $options
	 */
	public function merge($options) {
		/** @var Controller $controller */
		$controller = is_array($options) ? new Controller($options) : $options;

		$this->serializationGroups
			->merge($controller->getSerializationGroups());
	}

	/**
	 * @return SerializationGroups
	 */
	public function getSerializationGroups() {
		return $this->serializationGroups;
	}

	public function getSerializationGroup($action) {
		return $this->serializationGroups->getGroupsForAction($action);
	}

	public function hasSerializationGroup($action) {
		return $this->serializationGroups->hasGroupsForAction($action);
	}

	/**
	 * @param SerializationGroups $serializationGroups
	 */
	public function setSerializationGroups($serializationGroups) {
		if (is_array($serializationGroups)) {
			$serializationGroups = new SerializationGroups($serializationGroups);
		}

		$this->serializationGroups = $serializationGroups;
	}

	public function toArray() {
		return [
			'serialization_groups' => $this->serializationGroups->toArray()
		];
	}
}