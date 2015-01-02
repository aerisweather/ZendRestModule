<?php


namespace Aeris\ZendRestModule\Options;


use Zend\Stdlib\AbstractOptions;

class Annotations extends AbstractOptions {

	/** @var string */
	private $cacheDir;

	/** @var bool */
	private $debug;

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

}