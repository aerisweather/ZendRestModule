<?php


namespace Aeris\ZendRestModule\Options;


use Zend\Stdlib\AbstractOptions;

class Serializer extends AbstractOptions {

	/** @var string Cache Directory for serializer */
	private $cacheDir;

	/** @var bool */
	private $debug = false;

	/**
	 * Implement \JMS\Serializer\Handler\SubscribingHandlerInterface
	 * @var string[]
	 */
	private $handlers = [];

	/**
	 * Implements \JMS\Serializer\Naming\PropertyNamingStrategyInterface
	 * @var string
	 */
	private $namingStrategy = '\Aeris\ZendRestModule\Service\Serializer\Naming\IdenticalPropertyNamingStrategy';


	/**
	 * Implements \JMS\Serializer\Construction\ObjectConstructorInterface
	 *
	 * @var string
	 */
	private $objectConstructor = 'Aeris\ZendRestModule\Serializer\Constructor\InitializedObjectConstructor';

	/**
	 * Set to false to disable the @MaxDepth annotation.
	 *
	 * @var bool
	 */
	private $enableMaxDepth = true;

	/**
	 * @return string
	 */
	public function getCacheDir() {
		return $this->cacheDir;
	}

	/**
	 * @param string $cacheDir
	 */
	public function setCacheDir($cacheDir) {
		$this->cacheDir = $cacheDir;
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
	 * @return \string[]
	 */
	public function getHandlers() {
		return $this->handlers;
	}

	/**
	 * @param \string[] $handlers
	 */
	public function setHandlers($handlers) {
		$this->handlers = $handlers;
	}

	/**
	 * @return string
	 */
	public function getObjectConstructor() {
		return $this->objectConstructor;
	}

	/**
	 * @param string $objectConstructor
	 */
	public function setObjectConstructor($objectConstructor) {
		$this->objectConstructor = $objectConstructor;
	}

	/**
	 * @return string
	 */
	public function getNamingStrategy() {
		return $this->namingStrategy;
	}

	/**
	 * @param string $namingStrategy
	 */
	public function setNamingStrategy($namingStrategy) {
		$this->namingStrategy = $namingStrategy;
	}

	/**
	 * @return boolean
	 */
	public function isEnableMaxDepth() {
		return $this->enableMaxDepth;
	}

	/**
	 * @param boolean $enableMaxDepth
	 */
	public function setEnableMaxDepth($enableMaxDepth) {
		$this->enableMaxDepth = $enableMaxDepth;
	}

}