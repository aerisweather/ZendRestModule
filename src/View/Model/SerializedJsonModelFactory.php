<?php


namespace Aeris\ZendRestModule\View\Model;

use Aeris\ZendRestModule\Service\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;

class SerializedJsonModelFactory implements FactoryInterface {

	/**
	 * @param ServiceLocatorInterface $serviceLocator
	 * @return SerializationContext
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		/** @var ServiceManager $serviceLocator */
		/** @var SerializerInterface $serializer */
		$serializer = $serviceLocator->get('Aeris\ZendRestModule\Serializer');

		/** @var SerializationContext $context */
		$context = $serviceLocator
			->create('Aeris\ZendRestModule\Serializer\SerializationContext');

		$jsonModel = new SerializedJsonModel();
		$jsonModel->setSerializer($serializer);
		$jsonModel->setContext($context);

		return $jsonModel;
	}
}