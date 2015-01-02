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
			'Aeris\ZendRestModule\View\Model\SerializedJsonModel' => 'Aeris\ZendRestModule\View\Model\SerializedJsonModelFactory',
			'Aeris\ZendRestModule\Service\Serializer\SerializationContext' => '\Aeris\ZendRestModule\Service\Serializer\SerializationContextFactory',
			'Aeris\ZendRestModule\View\Http\RestExceptionStrategy' => '\Aeris\ZendRestModule\Service\RestExceptionStrategyFactory',
			'Aeris\ZendRestModule\Service\Annotation\AnnotationReader' => '\Aeris\ZendRestModule\Service\Annotation\AnnotationReaderFactory',
		],
	],
];
