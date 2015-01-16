<?php


namespace Aeris\ZendRestModule\Options\Listener;


use Aeris\ZendRestModule\Module;
use Aeris\ZendRestModule\Options\Controller as ControllerOptions;
use Aeris\ZendRestModule\Options\ZendRest;
use Aeris\ZendRestModule\Service\Annotation\Parser\SerializationGroups as SerializationGroupsParser;
use Doctrine\Common\Cache\Cache;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\Mvc\MvcEvent;

use Aeris\ZendRestModule\Options\ZendRest as ZendRestOptions;


/**
 * Parses annotated options for the
 * active controller.
 */
class ControllerListener {

	/** @var Cache */
	protected $cache;

	/** @var ZendRest */
	protected $zendRestOptions;

	/** @var SerializationGroupsParser */
	protected $annotationParser;

	/**
	 * @return ControllerListener
	 */
	public static function create() {
		return new self();
	}

	public function attachShared(SharedEventManagerInterface $eventManager) {
		$eventManager->attach(
			'Zend\Stdlib\DispatchableInterface',
			MvcEvent::EVENT_DISPATCH,
			[$this, 'updateControllerOptions'],
			-1
		);
	}

	public function updateControllerOptions(MvcEvent $evt) {
		$controllerName = $this->getDispatchedController($evt);
		$controllerOptions = $this->getOptions($controllerName);

		$this
			->zendRestOptions
			->mergeController($controllerName, $controllerOptions);
	}


	/**
	 * @param MvcEvent $evt
	 * @return mixed
	 * @throws \Exception
	 */
	protected function getDispatchedController(MvcEvent $evt) {
		$controllerName = $evt->getRouteMatch()->getParam('controller');

		if (!$controllerName) {
			throw new \Exception('Unable to parse ZendRestModule annotation:' .
				" The route match for \"{$evt->getName()}\" is missing a \"controller\" param.");
		}
		return $controllerName;
	}

	/**
	 * @param string $controllerName
	 * @return ControllerOptions
	 */
	protected function getOptions($controllerName) {
		// Get controller options from cache
		if (!$this->isDebug() && $options = $this->fetchCachedOptions($controllerName)) {
			return $options;
		}

		// Get controller options from annotations
		$controllerOptions = $this->parseAnnotations($controllerName);

		// Save controller options to cache,
		// for next time
		if (!$this->isDebug()) {
			$this->cacheOptions($controllerName, $controllerOptions);
		}

		return $controllerOptions;
	}

	/**
	 * @param string $controllerName
	 * @return ControllerOptions
	 */
	protected function parseAnnotations($controllerName) {
		$serializationGroups = $this->annotationParser
			->create($controllerName);

		return new ControllerOptions([
			'serialization_groups' => $serializationGroups,
		]);
	}

	/**
	 * @param string $controllerName
	 * @return string
	 */
	protected function getCacheKey($controllerName) {
		$controllersCacheNs = Module::CACHE_NAMESPACE . "controllers_";
		$controllerNameForCache = strtolower(
			str_replace('\\', '_', $controllerName)
		);

		return $controllersCacheNs . $controllerNameForCache;
	}

	/**
	 * @return bool
	 */
	protected function isDebug() {
		return $this->zendRestOptions->isDebug();
	}

	/**
	 * @param $controllerName
	 * @return ControllerOptions|false
	 */
	protected function fetchCachedOptions($controllerName) {
		$controllerCacheKey = $this->getCacheKey($controllerName);

		// Grab controller config from cache
		$config = $this->cache->fetch($controllerCacheKey);

		return new ControllerOptions($config);
	}

	/**
	 * Save controller options to cache.
	 *
	 * @param string $controllerName
	 * @param ControllerOptions $controllerOptions
	 */
	private function cacheOptions($controllerName, ControllerOptions $controllerOptions) {
		$cacheKey = $this->getCacheKey($controllerName);

		$this->cache->save($cacheKey, $controllerOptions->toArray());
	}

	/**
	 * @param Cache $cache
	 * @return $this
	 */
	public function setCache(Cache $cache) {
		$this->cache = $cache;
		return $this;
	}

	/**
	 * @param ZendRestOptions $zendRestOptions
	 * @return $this
	 */
	public function setZendRestOptions(ZendRest $zendRestOptions) {
		$this->zendRestOptions = $zendRestOptions;
		return $this;
	}

	/**
	 * @param SerializationGroupsParser $annotationParser
	 * @return $this
	 */
	public function setAnnotationParser(SerializationGroupsParser $annotationParser) {
		$this->annotationParser = $annotationParser;
		return $this;
	}

}