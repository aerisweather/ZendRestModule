<?php


namespace Aeris\ZendRestModule;


use Aeris\ZendRestModule\Service\Annotation\Parser\SerializationGroupCollection;
use Aeris\ZendRestModule\View\Listener\SerializedJsonViewModelListener;
use Zend\Mvc\MvcEvent;
use Aeris\ZendRestModule\Options\ZendRest as ZendRestOptions;

class Module {

	public function getConfig() {
		return include __DIR__ . '/config/module.config.php';
	}

	public function onBootstrap(MvcEvent $evt) {
		$eventManager = $evt->getApplication()
			->getEventManager();
		$sharedEventManager = $eventManager
			->getSharedManager();

		$serviceManager = $evt->getApplication()->getServiceManager();

		// Convert model objects into SerializedJsonViewModels
		$createSerializedJsonViewModelListener = new SerializedJsonViewModelListener();
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


		// Parse annotation
		/** @var ZendRestOptions $zendRestOptions */
		$zendRestOptions = $serviceManager->get('Aeris\ZendRestModule\Options\ZendRest');
		$serializationGroupsOptions = $zendRestOptions->getSerializationGroups();

		// Create serializationGroups options from annotations
		$serializationGroupsParser = $serviceManager
			->get('Aeris\ZendRestModule\Service\Annotation\Parser\SerializationGroupCollection');

		// Merge annotation options with existing options.
		$serializationGroupsOptions
			->merge($serializationGroupsParser->create());
	}
}
