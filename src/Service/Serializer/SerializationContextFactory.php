<?php


namespace Aeris\ZendRestModule\Service\Serializer;

use Aeris\ZendRestModule\Options\ZendRest as ZendRestOptions;
use JMS\Serializer\SerializationContext;
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
		/** @var ZendRestOptions $zendRestOptions */
		$zendRestOptions = $serviceLocator->get('Aeris\ZendRestModule\Options\ZendRest');
		$serializerOptions = $zendRestOptions->getSerializer();

		$context = SerializationContext::create();

		if ($serializerOptions->isEnableMaxDepth()) {
			$context->enableMaxDepthChecks();
		}

		return $context;
	}
}