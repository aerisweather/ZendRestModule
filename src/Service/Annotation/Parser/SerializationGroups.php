<?php


namespace Aeris\ZendRestModule\Service\Annotation\Parser;


use Aeris\ZendRestModule\Options\SerializationGroupCollection as SerializationGroupCollectionOptions;
use Aeris\ZendRestModule\Options\SerializationGroups as SerializationGroupsOptions;
use Doctrine\Common\Annotations\Reader;
use Zend\Di\ServiceLocator;
use Zend\Mvc\Controller\AbstractController;
use Aeris\ZendRestModule\View\Annotation\Groups as GroupsAnnotation;
use Zend\ServiceManager\ServiceManager;

class SerializationGroups {

	/** @var ServiceManager */
	protected $controllerManager;

	/** @var Reader */
	protected $annotationReader;

	/**
	 * Parses annotations on all application controllers,
	 * and creates a SerializationGroups options object.
	 *
	 * @return SerializationGroupsOptions
	 */
	public function create($controllerName) {
		/** @var AbstractController $controller */
		$controller = $this->controllerManager->get($controllerName);
		$actions = $this->getControllerActions($controller);

		$groups = array_reduce($actions, function($groups, $action) use ($controller) {
			$groups[$action] = $this->getAnnotatedGroups($controller, $action);
			return $groups;
		}, []);

		return new SerializationGroupsOptions($groups);
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

	private function getControllerActions(AbstractController $controller) {
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
	 * @param Reader $annotationReader
	 * @returns $this
	 */
	public function setAnnotationReader(Reader $annotationReader) {
		$this->annotationReader = $annotationReader;
		return $this;
	}

}