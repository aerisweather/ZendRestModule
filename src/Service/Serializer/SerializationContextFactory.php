<?php


namespace Aeris\ZendRestModule\Service\Serializer;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SerializationContextFactory implements FactoryInterface {

	/**
	 * Create service
	 *
	 * @param ServiceLocatorInterface $serviceLocator
	 * @return SerializationContext
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		return SerializationContext::create();
	}
}