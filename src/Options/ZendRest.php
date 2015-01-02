<?php


namespace Aeris\ZendRestModule\Options;


use Zend\Stdlib\AbstractOptions;
use Aeris\ZendRestModule\Options\Error as ErrorOptions;
use Aeris\ZendRestModule\Options\SerializationGroupCollection as SerializationGroupsOptions;
use Aeris\ZendRestModule\Options\Serializer as SerializerOptions;

class ZendRest extends AbstractOptions {
	/** @var string */
	private $cacheDir;

	/** @var Error[] */
	private $errors;

	/** @var SerializationGroupCollection */
	private $serializationGroups;

	/** @var Serializer */
	private $serializer;

	public function __construct($options = []) {
		$defaults = [
			'errors' => [],
			'serialization_groups' => [],
			'serializer' => []
		];

		parent::__construct(array_replace($defaults, $options));
	}

	/**
	 * @return mixed
	 */
	public function getCacheDir() {
		return $this->cacheDir;
	}

	/**
	 * @param mixed $cacheDir
	 */
	public function setCacheDir($cacheDir) {
		$this->cacheDir = $cacheDir;
	}

	/**
	 * @return Error[]
	 */
	public function getErrors() {
		return $this->errors;
	}

	/**
	 * @param Error[] $errors
	 */
	public function setErrors($errors) {
		$this->errors = array_map(function($errorConfig) {
			return new ErrorOptions($errorConfig);
		}, $errors);
	}

	/**
	 * @return SerializationGroupCollection[]
	 */
	public function getSerializationGroups() {
		return $this->serializationGroups;
	}

	/**
	 * @param SerializationGroupCollection $serializationGroups
	 */
	public function setSerializationGroups($serializationGroups) {
		$this->serializationGroups = new SerializationGroupsOptions($serializationGroups);
	}

	/**
	 * @return Serializer
	 */
	public function getSerializer() {
		return $this->serializer;
	}

	/**
	 * @param Serializer $serializer
	 */
	public function setSerializer($serializer) {
		$this->serializer = new SerializerOptions($serializer);
	}
}