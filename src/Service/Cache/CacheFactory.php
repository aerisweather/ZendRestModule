<?php


namespace Aeris\ZendRestModule\Service\Cache;


use Aeris\ZendRestModule\Options\ZendRest;
use Doctrine\Common\Cache\PhpFileCache;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CacheFactory implements FactoryInterface {

	/**
	 * Create service
	 *
	 * @param ServiceLocatorInterface $serviceLocator
	 * @return mixed
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		/** @var ZendRest $zendRestOptions */
		$zendRestOptions = $serviceLocator->get('Aeris\ZendRestModule\Options\ZendRest');
		$cacheDir = $zendRestOptions->getCacheDir();

		return new PhpFileCache($cacheDir . '/ZendRestModule');
	}
}