<?php
return [
	'modules' => [
		'Aeris\ZendRestModule',
		'Aeris\ZendRestModuleTest\RestTestModule'
	],

	'module_listener_options' => [
		'module_paths' => [
			__DIR__ . '/../',
			__DIR__ . '/../../../../../vendor'
		],
		'config_glob_paths' => [
			'config/autoload/{,*.}{global,local}.php',
		]
	]
];

