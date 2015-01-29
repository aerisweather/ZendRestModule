<?php


namespace Aeris\ZendRestModule\Service\Serializer;


use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Aeris\ZendRestModule\Options\ZendRest as ZendRestOptions;

class SerializerFactory implements FactoryInterface {

	/** @var ServiceLocatorInterface */
	protected $serviceLocator;
	

	/**
	 * Create service
	 *
	 * @param ServiceLocatorInterface $serviceLocator
	 * @return SerializerInterface
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		$this->serviceLocator = $serviceLocator;
		$this->registerSerializerAnnotations();

		/** @var ZendRestOptions $zendRestOptions */
		$zendRestOptions = $serviceLocator->get('Aeris\ZendRestModule\Options\ZendRest');
		$serializerOptions = $zendRestOptions->getSerializer();

		$config = array(
			'cacheDir' => $serializerOptions->getCacheDir(),
			'propertyNamingStrategy' => $this->createByName($serializerOptions->getNamingStrategy()),
			'objectConstructor' => $this->createByName($serializerOptions->getObjectConstructor()),
			'debug' => $serializerOptions->isDebug(),
			'extraHandlers' => array_map([$this, 'createByName'], $serializerOptions->getHandlers()),
			'subscribers' => array_map([$this, 'createByName'], $serializerOptions->getSubscribers()),
			'listeners' => $serializerOptions->getListeners(),
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
	protected function createByName($classRef, array $args = []) {
		// Is service
		// --> get from service locator
		if ($this->serviceLocator->has($classRef)) {
			return $this->serviceLocator->get($classRef);
		}
		// Is class name and has args
		// --> create instance with args
		if (count($args)) {
			$rClass = new \ReflectionClass($classRef);
			return $rClass->newInstanceArgs($args);
		}

		// Is class name (no args)
		// --> create instance
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