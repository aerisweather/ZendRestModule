<?php


namespace Aeris\ZendRestModule;


use Aeris\ZendRestModule\View\Listener\SerializedJsonViewModelListener;
use Doctrine\Common\Annotations\AnnotationReader;
use Zend\Mvc\Controller\ControllerManager;
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
			-1
		);


		// Catch controller exceptions,
		// and convert into JSON error objects.
		$restFulExceptionListener = $serviceManager->get('Aeris\ZendRestModule\View\Http\RestExceptionStrategy');
		$restFulExceptionListener->attach($eventManager);


		$sharedEventManager->attach(
			'Zend\Stdlib\DispatchableInterface',
			\Zend\Mvc\MvcEvent::EVENT_DISPATCH,
			[$this, 'onDispatch'],
			-1
		);
	}

	public function onDispatch(MvcEvent $evt) {
		$serviceManager = $evt
			->getApplication()
			->getServiceManager();


		// Get the active controller
		$controllerRef = $evt->getRouteMatch()->getParam('controller');
		$action = $evt->getRouteMatch()->getParam('action');
		/** @var ControllerManager $controllerManager */
		$controllerManager = $serviceManager->get('ControllerManager');
		$controller = $controllerManager->get($controllerRef);


		// Parse annotations
		/** @var AnnotationReader $reader */
		$reader = $serviceManager->get('Aeris\ZendRestModule\Service\Annotation\AnnotationReader');
		$rControllerClass = new \ReflectionClass($controller);
		$rActionMethod = $rControllerClass->getMethod($action);
		$methodAnnotations = $reader->getMethodAnnotation($rActionMethod, $rControllerClass);

		$foo = 'bar';
	}
}
