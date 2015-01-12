<?php


namespace Aeris\ZendRestModule\Service\Annotation;


use Aeris\ZendRestModule\Exception\ConfigurationException;
use Zend\Di\ServiceLocator;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\Controller\AbstractController;
use Zend\Mvc\MvcEvent;
use Doctrine\Common\Annotations\AnnotationReader;
use Zend\Mvc\Controller\ControllerManager;
use Aeris\ZendRestModule\View\Annotation\Groups;
use Aeris\ZendRestModule\Options\ZendRest as ZendRestOptions;

class AnnotationListener {

	/** @var ServiceLocator */
	protected $serviceManager;

	public function setSerializationGroupsFromAnnotations() {
		/** @var ZendRestOptions $zendRestOptions */
		$zendRestOptions = $this->serviceManager->get('Aeris\ZendRestModule\Options\ZendRest');
		$serializationGroups = $zendRestOptions->getSerializationGroups();

		/** @var ControllerManager $controllerManager */
		$controllerManager = $this->serviceManager->get('ControllerManager');
		$cNames = $controllerManager->getCanonicalNames();
		$controllers = [];
		foreach ($cNames as $name => $canonical) {
			$controllers[$name] = $controllerManager->get($canonical);
		}

		foreach ($controllers as $name => $controller) {
			$actions = $this->getControllerActions($controller);

			foreach ($actions as $action) {
				$actionAnnotations = $this->getMethodAnnotations($controller, $action);
				foreach ($actionAnnotations as $annotation) {
					if ($annotation instanceof Groups) {
						/** @var string[] $groups */
						$groups = $annotation->getGroups();

						$serializationGroups->addGroups($groups, $name, $action);
					}

				}
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

		// normalize action as method name
		$methodName = method_exists($controller, $action) ? $action : $action . 'Action';

		if (!method_exists($controller, $methodName)) {
			throw new ConfigurationException("Unable to parse annotation for method $controller::$methodName: " .
				"$methodName is not a valid method.");
		}

		$rActionMethod = $rControllerClass->getMethod($methodName);
		$methodAnnotations = $reader->getMethodAnnotations($rActionMethod);
		return $methodAnnotations;
	}

	private function getControllerActions($controller) {
		$methods = get_class_methods($controller);
		$restActions = [
			'create',
			'delete',
			'deleteList',
			'get',
			'getList',
			'head',
			'options',
			'patch',
			'replaceList',
			'patchList',
			'update',
		];

		return array_filter($methods, function ($methodName) use ($restActions) {
			return preg_match('/^(.*)Action$/', $methodName) === 1 || in_array($methodName, $restActions);
		});
	}

	/**
	 * @param ServiceLocator $serviceManager
	 */
	public function setServiceManager($serviceManager) {
		$this->serviceManager = $serviceManager;
	}

}