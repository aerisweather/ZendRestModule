<?php


namespace Aeris\ZendRestModule\Options;


use Zend\Stdlib\AbstractOptions;
use Aeris\ZendRestModule\Options\Error as ErrorOptions;
use Aeris\ZendRestModule\Options\SerializationGroupCollection as SerializationGroupsOptions;
use Aeris\ZendRestModule\Options\Serializer as SerializerOptions;
use Aeris\ZendRestModule\Options\Annotations as AnnotationOptions;

class ZendRest extends AbstractOptions {
	/** @var string */
	private $cacheDir;

	/** @var bool */
	private $debug = false;

	/** @var Error[] */
	private $errors;

	/** @var SerializationGroupCollection */
	private $serializationGroups;

	/** @var Serializer */
	private $serializer;

	/**
	 * @var Annotations
	 */
	private $annotations;

	public function __construct($options = []) {
		$defaults = [
			'errors' => [],
			'serialization_groups' => [],
			'serializer' => [],
			'annotations' => []
		];

		// We need to set these options before regular
		// hydration, because some nested options objects
		// share these configs with us.
		if (isset($options['cache_dir'])) {
			$this->setCacheDir($options['cache_dir']);
		}
		if (isset($options['debug'])) {
			$this->setDebug($options['debug']);
		}

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
	 * @return SerializationGroupCollection
	 */
	public function getSerializationGroups() {
		return $this->serializationGroups;
	}

	/**
	 * @param SerializationGroupCollection $serializationGroups
	 */
	public function setSerializationGroups(array $serializationGroups) {
		$this->serializationGroups = new SerializationGroupsOptions($serializationGroups);
	}

	/**
	 * @return Serializer
	 */
	public function getSerializer() {
		return $this->serializer;
	}

	/**
	 * @param array $serializerConfig
	 */
	public function setSerializer($serializerConfig) {
		// share our some common config with the serializer
		$serializerConfig = array_replace([
			'cache_dir' => $this->cacheDir,
			'debug' => $this->debug
		], $serializerConfig);

		$this->serializer = new SerializerOptions($serializerConfig);
	}

	/**
	 * @return boolean
	 */
	public function isDebug() {
		return $this->debug;
	}

	/**
	 * @param boolean $debug
	 */
	public function setDebug($debug) {
		$this->debug = $debug;
	}

	/**
	 * @return Annotations
	 */
	public function getAnnotations() {
		return $this->annotations;
	}

	/**
	 * @param array $annotationsConfig
	 */
	public function setAnnotations(array $annotationsConfig) {
		// share our some common config with the annotation reader
		$annotationsConfig = array_replace([
			'cache_dir' => $this->cacheDir,
			'debug' => $this->debug,
		], $annotationsConfig);

		$this->annotations = new AnnotationOptions($annotationsConfig);
	}
}