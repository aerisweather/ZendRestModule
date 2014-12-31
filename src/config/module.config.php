<?php
if (!function_exists('findParentPath')) {
	function findParentPath($path) {
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
	};
}

$getRestApiConfig = function($serviceManager, $prop) {
	$config = $serviceManager->get('config');
	if (!isset($config['zend-rest'])) {
		throw new \Exception('Missing zend-rest configuration.');
	}
	if (!isset($config['zend-rest'][$prop])) {
		throw new \Exception("Missing `$prop` in zend-rest config`");
	}
	return $config['zend-rest'][$prop];
};

return [
	'view_manager' => [
		'strategies' => [
			'ViewJsonStrategy'
		]
	],
	'service_manager' => [
		'factories' => [
			'view_serializer' => function (Zend\ServiceManager\ServiceManager $serviceManager) use ($getRestApiConfig) {
				$vendorPath = findParentPath('vendor');
				$serializerPath = $vendorPath . '/jms/serializer/src';

				if (!is_dir($serializerPath)) {
					die('Unable to find JMS serializer path. Sorry.');
				}

				// Doctrine Annotations does not use normal PSR-0 autoloading,
				// so we have to register stuff ourselves.
				// See: http://stackoverflow.com/questions/14629137/jmsserializer-stand-alone-annotation-does-not-exist-or-cannot-be-auto-loaded/
				\Doctrine\Common\Annotations\AnnotationRegistry::registerAutoloadNamespace(
					'JMS\Serializer\Annotation',
					$serializerPath
				);

				$namingStrategy = new \Aeris\ZendRestModule\Service\Serializer\Naming\IdenticalPropertyNamingStrategy();

				$config = array(
					'cacheDir' => $getRestApiConfig($serviceManager, 'cache_dir'),
					'propertyNamingStrategy' => $namingStrategy,
					'objectConstructor' => new \Aeris\ZendRestModule\Service\Serializer\Constructor\InitializedObjectConstructor(new \JMS\Serializer\Construction\UnserializeObjectConstructor()),
					'debug' => true,
					'extraHandlers' => [new \Aeris\ZendRestModule\Service\Serializer\Handler\DateTimeTimestampHandler()]
				);
				try {
					$serializer = new \Aeris\ZendRestModule\Service\Serializer\Serializer($config);
				}
				catch (JMS\Serializer\Exception\RuntimeException $error) {
					// die, loudly.
					error_log($error);
					exit(1);
				}
				return $serializer;
			},
			'RestfulExceptionStrategy' => function (\Zend\ServiceManager\ServiceManager $serviceManager) use ($getRestApiConfig) {
				$errorConfig = $getRestApiConfig($serviceManager, 'errors');
				return new \Aeris\ZendRestModule\View\Http\RestfulExceptionStrategy($errorConfig);
			},
		],
		'invokables' => array(
			'SerializedJsonModel' => 'Aeris\ZendRestModule\View\Model\SerializedJsonModel'
		),
		'initializers' => array(
			// Inject jms_serializer into SerializerAwareInterface instances
			'SerializerAwareInterface' => function ($model, \Zend\ServiceManager\ServiceManager $serviceLocator) {
				if ($model instanceof \Aeris\ZendRestModule\View\Model\SerializerAwareInterface) {
					$serializerService = $serviceLocator->get('view_serializer');
					$model->setSerializer($serializerService);
				}
			}
		)
	],
];
