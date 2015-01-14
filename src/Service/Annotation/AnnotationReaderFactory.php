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

		$annotationsDir = __DIR__ . '/../../View/Annotation';

		AnnotationRegistry::registerFile(
			$annotationsDir . '/Groups.php'
		);

		$reader = new \Doctrine\Common\Annotations\AnnotationReader();

		// Do not use cache when in debug mode.
		if ($this->options->isDebug()) {
			return $reader;
		}

		$cache = $serviceLocator->get('Aeris\ZendRestModule\Cache');

		return new CachedReader(
			$reader,
			$cache,
			$debug = $this->options->isDebug()
		);
	}
}