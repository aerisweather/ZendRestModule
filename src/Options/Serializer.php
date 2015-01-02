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
	private $objectConstructor = 'Aeris\ZendRestModule\Service\Serializer\Constructor\InitializedObjectConstructor';

	/**
	 * Implements \Aeris\ZendRestModule\Service\Serializer\SerializerInterface
	 *
	 * @var string
	 */
	private $serializer = '\Aeris\ZendRestModule\Service\Serializer\Serializer';

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
	 * @return string
	 */
	public function getSerializer() {
		return $this->serializer;
	}

	/**
	 * @param string $serializer
	 */
	public function setSerializer($serializer) {
		$this->serializer = $serializer;
	}

}