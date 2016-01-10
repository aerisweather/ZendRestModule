<?php


namespace Aeris\ZendRestModule;

use Aeris\ZendRestModule\View\Listener\SerializedJsonModelListener;
use Zend\Mvc\MvcEvent;

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
	
		// only change the response if we handle JSON Requests
		if($this->checkJson($evt)) {
			// Parse Controller annotations
			$controllerOptionsListener = $serviceManager
				->get('Aeris\ZendRestModule\Options\Listener\ControllerListener');
			$controllerOptionsListener->attachShared($sharedEventManager);
		
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
		}
	}
	
	/*
	 * Check if response wants JSON
	 */
    	public function checkJson($evt)
	{
		$response = $evt->getResponse();
	        $headers  = $response->getHeaders();
	        if (!$headers->has('Content-Type')) {
	            return false;
	        }
	        
	        $contentType = $headers->get('Content-Type');
	        $value       = $contentType->getFieldValue();
	        if (false !== strpos('application/json', $value)) {
	            return false;
	        }
	        
	        return true;
	}
}
