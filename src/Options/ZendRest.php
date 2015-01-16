<?php


namespace Aeris\ZendRestModule\Options;


use Aeris\ZendRestModule\Exception\ConfigurationException;
use Zend\Stdlib\AbstractOptions;
use Aeris\ZendRestModule\Options\Error as ErrorOptions;
use Aeris\ZendRestModule\Options\Serializer as SerializerOptions;
use Aeris\ZendRestModule\Options\Controller as ControllerOptions;

class ZendRest extends AbstractOptions {
	/** @var string */
	private $cacheDir;

	/** @var bool */
	private $debug = false;

	/** @var Error[] */
	private $errors;

	/** @var Serializer */
	private $serializer;

	/** @var Controller[] */
	private $controllers;

	public function __construct($options = []) {
		$defaults = [
			'errors' => [],
			'controllers' => [],
			'serializer' => [],
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
	 * @return ControllerOptions[]
	 */
	public function getControllers() {
		return $this->controllers;
	}

	/**
	 * @param $name
	 * @return bool
	 */
	public function hasController($name) {
		return isset($this->controllers[$name]);
	}

	/**
	 * @param string $name
	 * @return ControllerOptions
	 * @throws ConfigurationException
	 */
	public function getController($name) {
		if (!isset($this->controllers[$name])) {
			throw new ConfigurationException("Unable to find controller options for '$name'.'");
		}

		return $this->controllers[$name];
	}

	/**
	 * @param mixed $controllers
	 */
	public function setControllers($controllers) {
		$this->controllers = [];

		foreach ($controllers as $controllerName => $options) {
			// for controllers without configuration.
			// Allows config to mix associative and non-associative values.
			if (is_string($options)) {
				$controllerName = $options;
				$options = [];
			}

			$this->controllers[$controllerName] = new ControllerOptions($options);
		}
	}

	/**
	 * @param $controllerName
	 * @param array|ControllerOptions $controllerOptions
	 */
	public function setController($controllerName, $controllerOptions = []) {
		$controllerOptions = $controllerOptions instanceof ControllerOptions ?
			$controllerOptions : new ControllerOptions($controllerOptions);

		$this->controllers[$controllerName] = $controllerOptions;
	}

	/**
	 * @param $controllerName
	 * @param array|ControllerOptions $controllerOptions
	 * @throws ConfigurationException
	 */
	public function mergeController($controllerName, $controllerOptions) {
		if (!$this->hasController($controllerName)) {
			$this->setController($controllerName, $controllerOptions);
		}

		$this->getController($controllerName)
			->merge($controllerOptions);
	}

	/**
	 * @param string $controllerName
	 * @param string $action
	 * @return array|null
	 * @throws ConfigurationException
	 */
	public function getSerializationGroups($controllerName, $action) {
		if (!$this->hasController($controllerName)) {
			return null;
		}

		$controllerOptions = $this->getController($controllerName);
		if (!$controllerOptions->hasSerializationGroup($action)) {
			return null;
		}

		return $controllerOptions->getSerializationGroup($action);
	}
}