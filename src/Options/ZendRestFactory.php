<?php


namespace Aeris\ZendRestModule\Options;


use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Aeris\ZendRestModule\Options\ZendRest as ZendRestOptions;

class ZendRestFactory implements FactoryInterface {

	/**
	 * Create service
	 *
	 * @param ServiceLocatorInterface $serviceLocator
	 * @return mixed
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		$config = $serviceLocator->get('config');
		$zendRestConfig = isset($config['zend_rest']) ? $config['zend_rest'] : null;

		return new ZendRestOptions($zendRestConfig);
	}
}