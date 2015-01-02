<?php


namespace Aeris\ZendRestModule\Service\Annotation;


use Aeris\ZendRestModule\Options\ZendRest as ZendRestOptions;
use Aeris\ZendRestModule\Options\Annotations as AnnotationOptions;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Cache\PhpFileCache;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AnnotationReaderFactory implements FactoryInterface {

	/**
	 * @var AnnotationOptions
	 */
	protected $options;

	/**
	 * @param ServiceLocatorInterface $serviceLocator
	 * @return mixed
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		/** @var ZendRestOptions $zendRestOptions */
		$zendRestOptions = $serviceLocator
			->get('Aeris\ZendRestModule\Options\ZendRest');
		$this->options = $zendRestOptions->getAnnotations();


		AnnotationRegistry::registerAutoloadNamespace(
			'Aeris\ZendRestModule\View\Annotation',
			__DIR__ . '/../../../src'
		);

		return new CachedReader(
			new \Doctrine\Common\Annotations\AnnotationReader(),
			new PhpFileCache($this->options->getCacheDir()),
			$debug = $this->options->isDebug()
		);
	}
}