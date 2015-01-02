<?php


namespace Aeris\ZendRestModule\Service\Annotation;


use Zend\Di\ServiceLocator;
use Zend\Mvc\Controller\AbstractController;
use Zend\Mvc\MvcEvent;
use Doctrine\Common\Annotations\AnnotationReader;
use Zend\Mvc\Controller\ControllerManager;
use Aeris\ZendRestModule\View\Annotation\Groups;
use Aeris\ZendRestModule\Options\ZendRest as ZendRestOptions;

class AnnotationListener {

	/** @var ServiceLocator */
	protected $serviceManager;

	public function onDispatch(MvcEvent $evt) {
		$this->serviceManager = $evt
			->getApplication()
			->getServiceManager();

		/** @var ZendRestOptions $zendRestOptions */
		$zendRestOptions = $this->serviceManager->get('Aeris\ZendRestModule\Options\ZendRest');
		$serializationGroups = $zendRestOptions->getSerializationGroups();


		$controllerRef = $evt->getRouteMatch()->getParam('controller');
		$action = $evt->getRouteMatch()->getParam('action');

		// Get the active controller
		/** @var ControllerManager $controllerManager */
		$controllerManager = $this->serviceManager->get('ControllerManager');
		$controller = $controllerManager->get($controllerRef);


		// Parse annotations
		$methodAnnotations = $this->getMethodAnnotations($controller, $action);
		foreach ($methodAnnotations as $annotation) {
			if ($annotation instanceof Groups) {
				/** @var string[] $groups */
				$groups = $annotation->getGroups();

				$serializationGroups->addGroups($groups, $controllerRef, $action);
			}
		}
	}

	/**
	 * @param AbstractController $controller
	 * @param string $action
	 * @return array
	 */
	public function getMethodAnnotations($controller, $action) {
		/** @var AnnotationReader $reader */
		$reader = $this->serviceManager->get('Aeris\ZendRestModule\Service\Annotation\AnnotationReader');
		$rControllerClass = new \ReflectionClass($controller);
		$rActionMethod = $rControllerClass->getMethod($action);
		$methodAnnotations = $reader->getMethodAnnotations($rActionMethod);
		return $methodAnnotations;
	}

}