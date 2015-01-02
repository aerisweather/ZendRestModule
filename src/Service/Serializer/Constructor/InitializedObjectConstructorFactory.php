<?php


namespace Aeris\ZendRestModule\Service\Serializer\Constructor;


use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class InitializedObjectConstructorFactory implements FactoryInterface {

	/**
	 * Create service
	 *
	 * @param ServiceLocatorInterface $serviceLocator
	 * @return mixed
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		$fallbackConstructor = new \JMS\Serializer\Construction\UnserializeObjectConstructor();
		return new InitializedObjectConstructor($fallbackConstructor);
	}
}