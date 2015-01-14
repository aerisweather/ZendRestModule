<?php


namespace Aeris\ZendRestModule\Service\Annotation\Parser;


use Doctrine\Common\Annotations\AnnotationReader;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SerializationGroupCollectionFactory implements FactoryInterface {

	/**
	 * Create service
	 *
	 * @param ServiceLocatorInterface $serviceLocator
	 * @return mixed
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		$serializationGroupsParser = new SerializationGroupCollection();

		/** @var ControllerManager $controllerManager */
		$controllerManager = $serviceLocator->get('ControllerManager');
		/** @var AnnotationReader $annotationReader */
		$annotationReader = $serviceLocator->get('Aeris\ZendRestModule\Annotation\AnnotationReader');

		return $serializationGroupsParser
			->setControllerManager($controllerManager)
			->setAnnotationReader($annotationReader);
	}
}