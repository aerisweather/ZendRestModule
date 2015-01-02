<?php
return [
	'view_manager' => [
		'strategies' => [
			'ViewJsonStrategy'
		]
	],
	'service_manager' => [
		'factories' => [
			'Aeris\ZendRestModule\Options\ZendRest' => '\Aeris\ZendRestModule\Options\ZendRestFactory',
			'Aeris\ZendRestModule\Service\Serializer\Constructor\InitializedObjectConstructor' => '\Aeris\ZendRestModule\Service\Serializer\Constructor\InitializedObjectConstructorFactory',
			'Aeris\ZendRestModule\Service\Serializer' => '\Aeris\ZendRestModule\Service\Serializer\SerializerFactory',
			'Aeris\ZendRestModule\View\Http\RestExceptionStrategy' => '\Aeris\ZendRestModule\Service\RestExceptionStrategyFactory',
		],
		'invokables' => [
			'Aeris\ZendRestModule\View\Model\SerializedJsonModel' => 'Aeris\ZendRestModule\View\Model\SerializedJsonModel'
		],
		'initializers' => [
			// Inject jms_serializer into SerializerAwareInterface instances
			'SerializerAwareInterface' => function ($model, \Zend\ServiceManager\ServiceManager $serviceLocator) {
				if ($model instanceof \Aeris\ZendRestModule\View\Model\SerializerAwareInterface) {
					$serializerService = $serviceLocator->get('view_serializer');
					$model->setSerializer($serializerService);
				}
			}
		],
		'aliases' => [
			'view_serializer' => 'Aeris\ZendRestModule\Service\Serializer',
			'SerializedJsonModel' => 'Aeris\ZendRestModule\View\Model\SerializedJsonModel',
		]
	],
];
