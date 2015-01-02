<?php


namespace Aeris\ZendRestModule;


use Aeris\ZendRestModule\Service\Annotation\AnnotationListener;
use Aeris\ZendRestModule\View\Listener\SerializedJsonViewModelListener;
use Zend\Mvc\MvcEvent;

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
		$annotationListener = new AnnotationListener();
		$sharedEventManager->attach(
			'Zend\Stdlib\DispatchableInterface',
			\Zend\Mvc\MvcEvent::EVENT_DISPATCH,
			[$annotationListener, 'onDispatch'],
			-1
		);
	}
}
