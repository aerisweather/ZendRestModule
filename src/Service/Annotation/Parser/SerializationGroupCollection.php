<?php


namespace Aeris\ZendRestModule\Service\Annotation\Parser;


use Aeris\ZendRestModule\Options\SerializationGroupCollection as SerializationGroupCollectionOptions;
use Zend\Di\ServiceLocator;
use Zend\Mvc\Controller\AbstractController;
use Doctrine\Common\Annotations\AnnotationReader;
use Aeris\ZendRestModule\View\Annotation\Groups as GroupsAnnotation;
use Zend\ServiceManager\ServiceManager;

class SerializationGroupCollection {

	/** @var ServiceManager */
	protected $controllerManager;

	/** @var AnnotationReader */
	protected $annotationReader;

	/**
	 * Parses annotations on all application controllers,
	 * and creates a SerializationGroupCollection options object.
	 *
	 * @return SerializationGroupCollection
	 */
	public function create() {
		$groups = [];

		$controllers = $this->getApplicationControllers();

		foreach ($controllers as $controllerRef => $controller) {
			$actions = $this->getControllerActions($controller);

			$groups[$controllerRef] = array_reduce($actions, function($controllerGroups, $action) use ($controller) {
				$controllerGroups[$action] = $this->getAnnotatedGroups($controller, $action);

				return $controllerGroups;
			}, []);

		}

		return new SerializationGroupCollectionOptions($groups);
	}

	/**
	 * Return all controllers registered with the
	 * application ControllerManager.
	 *
	 * @return AbstractController[] Indexed by service manager invokable reference.
	 */
	private function getApplicationControllers() {
		$controllerRefs = $this->controllerManager->getCanonicalNames();

		$controllers = [];
		foreach ($controllerRefs as $ref => $canonical) {
			$controllers[$ref] = $this->controllerManager->get($canonical);
		}

		return $controllers;
	}

	/**
	 * @param AbstractController $controller
	 * @param string $actionMethod Method name
	 * @return string[] A list of annotated groups for the method
	 */
	public function getAnnotatedGroups(AbstractController $controller, $actionMethod) {
		return array_reduce($this->getGroupsAnnotations($controller, $actionMethod), function(array $groups, GroupsAnnotation $annotation) {
			 return array_merge($groups, $annotation->getGroups());
		}, []);
	}

	/**
	 * @param AbstractController $controller
	 * @param $method
	 * @return GroupsAnnotation[]  All @Group() annotations for the controller method
	 */
	public function getGroupsAnnotations(AbstractController $controller, $method) {
		return array_filter($this->getMethodAnnotations($controller, $method), function($annotation) {
			return $annotation instanceof GroupsAnnotation;
		});
	}

	/**
	 * @param AbstractController $controller
	 * @param string $method
	 * @return Object[] Annotation objects
	 */
	public function getMethodAnnotations(AbstractController $controller, $method) {
		$rControllerClass = new \ReflectionClass($controller);
		$rMethod = $rControllerClass->getMethod($method);

		return $this->annotationReader->getMethodAnnotations($rMethod);
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
	 * @param ServiceManager $controllerManager
	 * @returns $this
	 */
	public function setControllerManager(ServiceManager $controllerManager) {
		$this->controllerManager = $controllerManager;
		return $this;
	}

	/**
	 * @param AnnotationReader $annotationReader
	 * @returns $this
	 */
	public function setAnnotationReader(AnnotationReader $annotationReader) {
		$this->annotationReader = $annotationReader;
		return $this;
	}

}