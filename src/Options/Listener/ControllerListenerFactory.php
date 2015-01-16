<?php


namespace Aeris\ZendRestModule\Options\Listener;


use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Aeris\ZendRestModule\Options\ZendRest as ZendRestOptions;
use Aeris\ZendRestModule\Service\Annotation\Parser\SerializationGroups as SerializationGroupCollectionParser;

class ControllerListenerFactory implements FactoryInterface {

	/**
	 * Create service
	 *
	 * @param ServiceLocatorInterface $serviceLocator
	 * @return mixed
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		/** @var \Doctrine\Common\Cache\Cache $cache */
		$cache = $serviceLocator->get('Aeris\ZendRestModule\Cache');

		/** @var ZendRestOptions $zendRestOptions */
		$zendRestOptions = $serviceLocator
			->get('Aeris\ZendRestModule\Options\ZendRest');

		/** @var SerializationGroupCollectionParser $serializationGroupsParser */
		$serializationGroupsParser = $serviceLocator
			->get('Aeris\ZendRestModule\Annotation\Parser\SerializationGroupCollection');

		return ControllerListener::create()
			->setCache($cache)
			->setZendRestOptions($zendRestOptions)
			->setAnnotationParser($serializationGroupsParser);
	}
}