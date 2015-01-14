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
	public function setSerializationGroups(array $serializationGroups) {
		$this->serializationGroups = new SerializationGroups($serializationGroups);
	}

	public function toArray() {
		return [
			'serialization_groups' => $this->serializationGroups->toArray()
		];
	}
}