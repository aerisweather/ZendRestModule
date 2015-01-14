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
			'Aeris\ZendRestModule\Serializer\Constructor\InitializedObjectConstructor' => '\Aeris\ZendRestModule\Service\Serializer\Constructor\InitializedObjectConstructorFactory',
			'Aeris\ZendRestModule\Serializer' => '\Aeris\ZendRestModule\Service\Serializer\SerializerFactory',
			'Aeris\ZendRestModule\View\Model\SerializedJsonModel' => 'Aeris\ZendRestModule\View\Model\SerializedJsonModelFactory',
			'Aeris\ZendRestModule\Serializer\SerializationContext' => '\Aeris\ZendRestModule\Service\Serializer\SerializationContextFactory',
			'Aeris\ZendRestModule\View\Http\RestExceptionStrategy' => '\Aeris\ZendRestModule\Service\RestExceptionStrategyFactory',
			'Aeris\ZendRestModule\Annotation\AnnotationReader' => '\Aeris\ZendRestModule\Service\Annotation\AnnotationReaderFactory',
			'Aeris\ZendRestModule\Annotation\Parser\SerializationGroupCollection' => '\Aeris\ZendRestModule\Service\Annotation\Parser\SerializationGroupCollectionFactory',
			'Aeris\ZendRestModule\Cache' => '\Aeris\ZendRestModule\Service\Cache\CacheFactory',
		],
	],
];
