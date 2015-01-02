<?php


namespace Aeris\ZendRestModule\Service;


use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Aeris\ZendRestModule\Options\ZendRest as ZendRestOptions;
use Aeris\ZendRestModule\Options\Error as ErrorOptions;

class RestExceptionStrategyFactory implements FactoryInterface {

	/**
	 * Create service
	 *
	 * @param ServiceLocatorInterface $serviceLocator
	 * @return mixed
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		/** @var ZendRestOptions $zendRestOptions */
		$zendRestOptions = $serviceLocator->get('Aeris\ZendRestModule\Options\ZendRest');

		// Convert options to array
		$errorConfig = array_map(function(ErrorOptions $error) {
			return $error->toArray();
		}, $zendRestOptions->getErrors());

		return new \Aeris\ZendRestModule\View\Http\RestExceptionStrategy($errorConfig);
	}
}