<?php


namespace Aeris\ZendRestModule\Service\Annotation\Parser;


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

		return $serializationGroupsParser
			->setControllerManager($serviceLocator->get('ControllerManager'))
			->setAnnotationReader($serviceLocator->get('Aeris\ZendRestModule\Annotation\AnnotationReader'));
	}
}