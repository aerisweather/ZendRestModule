<?php


namespace Aeris\ZendRestModule\Service\Serializer;


use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Aeris\ZendRestModule\Options\ZendRest as ZendRestOptions;

class SerializerFactory implements FactoryInterface {

	/**
	 * Create service
	 *
	 * @param ServiceLocatorInterface $serviceLocator
	 * @return SerializerInterface
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		$this->registerSerializerAnnotations();

		/** @var ZendRestOptions $zendRestOptions */
		$zendRestOptions = $serviceLocator->get('Aeris\ZendRestModule\Options\ZendRest');
		$serializerOptions = $zendRestOptions->getSerializer();

		$config = array(
			'cacheDir' => $serializerOptions->getCacheDir(),
			'propertyNamingStrategy' => $this->createByName($serviceLocator, $serializerOptions->getNamingStrategy()),
			'objectConstructor' => $this->createByName($serviceLocator, $serializerOptions->getObjectConstructor()),
			'debug' => $serializerOptions->isDebug(),
			'extraHandlers' => array_map(function($handler) use ($serviceLocator) {
				return $this->createByName($serviceLocator, $handler);
			}, $serializerOptions->getHandlers()),
		);

		try {
			$serializer = new Serializer($config);
		}
		catch (\JMS\Serializer\Exception\RuntimeException $error) {
			// die, loudly.
			error_log($error);
			exit(1);
		}
		return $serializer;
	}


	/**
	 * Returns an instance of the provided class.
	 * If the $classRef is a configured service, return the service instead.
	 *
	 * @param ServiceLocatorInterface $serviceLocator
	 * @param string $classRef
	 * @param array $args Arguments to pass to the class constructor
	 * @return object
	 */
	protected function createByName(ServiceLocatorInterface $serviceLocator, $classRef, array $args = []) {
		if ($serviceLocator->has($classRef)) {
			return $serviceLocator->get($classRef);
		}
		if (count($args)) {
			$rClass = new \ReflectionClass($classRef);
			return $rClass->newInstanceArgs($args);
		}

		return new $classRef;
	}

	/**
	 * @return string
	 */
	private function getSerializerPath() {
		$vendorPath = self::findParentPath('vendor');
		$serializerPath = $vendorPath . '/jms/serializer/src';

		if (!is_dir($serializerPath)) {
			die('Unable to find JMS serializer path. Sorry.');
		}
		return $serializerPath;
	}

	/**
	 * @param string $path
	 * @return bool|string
	 */
	private static function findParentPath($path) {
		$dir = __DIR__;
		$previousDir = '.';

		while (!is_dir($dir . '/' . $path)) {
			$dir = dirname($dir);
			if ($previousDir === $dir) {
				return false;
			}
			$previousDir = $dir;
		}

		return $dir . '/' . $path;
	}

	private function registerSerializerAnnotations() {
		$serializerPath = $this->getSerializerPath();

		// Doctrine Annotations does not use normal PSR-0 autoloading,
		// so we have to register stuff ourselves.
		// See: http://stackoverflow.com/questions/14629137/jmsserializer-stand-alone-annotation-does-not-exist-or-cannot-be-auto-loaded/
		\Doctrine\Common\Annotations\AnnotationRegistry::registerAutoloadNamespace(
			'JMS\Serializer\Annotation',
			$serializerPath
		);
	}
}