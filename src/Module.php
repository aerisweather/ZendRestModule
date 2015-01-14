<?php


namespace Aeris\ZendRestModule;


use Aeris\ZendRestModule\Options\SerializationGroupCollection;
use Aeris\ZendRestModule\Service\Annotation\Parser\SerializationGroups as SerializationGroupCollectionParser;
use Aeris\ZendRestModule\View\Listener\SerializedJsonModelListener;
use Zend\Mvc\MvcEvent;
use Aeris\ZendRestModule\Options\ZendRest as ZendRestOptions;
use Zend\ServiceManager\ServiceManager;

class Module {

	public function getConfig() {
		return include __DIR__ . '/config/module.config.php';
	}

	const CACHE_NAMESPACE = 'aeris_zend_rest_module_';

	public function onBootstrap(MvcEvent $evt) {
		$eventManager = $evt->getApplication()
			->getEventManager();
		$sharedEventManager = $eventManager
			->getSharedManager();

		$serviceManager = $evt->getApplication()->getServiceManager();

		// Convert controller return valuesinto SerializedJsonModels
		$createSerializedJsonViewModelListener = new SerializedJsonModelListener();
		$sharedEventManager->attach(
			'Zend\Stdlib\DispatchableInterface',
			MvcEvent::EVENT_DISPATCH,
			array($createSerializedJsonViewModelListener, 'updateViewModelFromResult'),
			-2
		);


		// Catch controller exceptions,
		// and convert into JSON error objects.
		$restFulExceptionListener = $serviceManager->get('Aeris\ZendRestModule\View\Http\RestExceptionStrategy');
		$restFulExceptionListener->attach($eventManager);
		$this->initializeSerializationGroupOptions($serviceManager);


	}

	/**
	 * @param ServiceManager $serviceManager
	 */
	protected function initializeSerializationGroupOptions(ServiceManager $serviceManager) {
		/** @var ZendRestOptions $zendRestOptions */
		$zendRestOptions = $serviceManager->get('Aeris\ZendRestModule\Options\ZendRest');

		/** @var \Doctrine\Common\Cache\Cache $cache */
		$cache = $serviceManager->get('Aeris\ZendRestModule\Cache');
		$cacheId = self::CACHE_NAMESPACE . 'controllers';

		// Use cached options, if available
		$isConfigCached = $cache->contains($cacheId);
		if (!$zendRestOptions->isDebug() && $isConfigCached) {
			$json = $cache->fetch($cacheId);
			$controllersConfig = SerializationGroupCollection::deserialize($json);
			$zendRestOptions->setControllers($controllersConfig);
			return;
		}

		/** @var SerializationGroupCollectionParser $serializationGroupsParser */
		$serializationGroupsParser = $serviceManager
			->get('Aeris\ZendRestModule\Annotation\Parser\SerializationGroupCollection');

		// Parse annotated groups,
		// and merge with configured groups
		$controllersConfig = [];
		foreach ($zendRestOptions->getControllers() as $controllerName => &$controllerOptions) {
			$annotatedGroups = $serializationGroupsParser->create($controllerName);
			$configuredGroups = $controllerOptions->getSerializationGroups();

			$configuredGroups->merge($annotatedGroups);

			// Save this for cache
			$controllersConfig[$controllerName] = $controllerOptions->toArray();
		}

		// Save serialization groups to cache

		$cache->save($cacheId, json_encode($controllersConfig));
	}
}
