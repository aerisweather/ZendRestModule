<?php

use Aeris\ZendRestModule\Event\RestErrorEvent;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\RequestInterface;
use Zend\View\Model\ViewModel;

$GLOBALS['zend-rest-test-errors-not-found-onerror-was-called'] = false;
$GLOBALS['zend-rest-errors-not-found-on-error-args'] = null;


return [
	'paramsForTestedRoute' => [
		'animalType' => 'monkey'
	],
	'zend-rest' => [
		'cache_dir' => __DIR__ . '/../../../../../../data/cache',
		'errors' => [
			[
				'error' => \Zend\Mvc\Application::ERROR_ROUTER_NO_MATCH,
				'httpCode' => 404,
				'applicationCode' => 'invalid_request',
				'details' => 'The requested endpoint or action is invalid and not supported.',
			],
			[
				'error' => '\Aeris\ZendRestModuleTest\RestTestModule\Exception\NotFoundException',
				'httpCode' => 404,
				'applicationCode' => 'not_found',
				'details' => function (\Aeris\ZendRestModuleTest\RestTestModule\Exception\NotFoundException $error) {
					return 'foo';
				},
				'onError' => function () {
					$GLOBALS['zend-rest-test-errors-not-found-onerror-was-called'] = true;
					$GLOBALS['zend-rest-errors-not-found-on-error-args'] = func_get_args();
				}
			],
			[
				'error' => '\Aeris\ZendRestModuleTest\RestTestModule\Exception\ForModifyingViewModelTestException',
				'httpCode' => 500,
				'applicationCode' => 'for_modifying_view_model_test',
				'onError' => function (RestErrorEvent $evt) {
					/** @var ViewModel $viewModel */
					$viewModel = $evt->getViewModel();

					// modify the error object
					$errorObj = $viewModel->getVariable('error');
					$errorObj['foo'] = 'bar';
					$viewModel->setVariable('error', $errorObj);

					// add a view  model property
					$viewModel->setVariable('foosies', ['barsy', 'shazlamy']);
				}
			],
			[
				'error' => '\Exception',
				'httpCode' => 500,
				'applicationCode' => 'server_error',
				'details' => 'Something bad happened.',
				'onError' => function(RestErrorEvent $evt) {
					// Set a breakpoint here for easy debugging
					$error = $evt->getError();
					$foo = 'bar';
				}
			]
		],
	],
	'controllers' => [
		'invokables' => [
			'Aeris\ZendRestModuleTest\RestTestModule\Controller\AnimalRest' => 'Aeris\ZendRestModuleTest\RestTestModule\Controller\AnimalRestController',
			'Aeris\ZendRestModuleTest\RestTestModule\Controller\Animal' => 'Aeris\ZendRestModuleTest\RestTestModule\Controller\AnimalController',
			'Aeris\ZendRestModuleTest\RestTestModule\Controller\UserRest' => '\Aeris\ZendRestModuleTest\RestTestModule\Controller\UserRestController'
		],
	],
	'router' => [
		'routes' => [
			'users' => [
				'type' => 'Aeris\ZendRestModule\Mvc\Router\Http\RestSegment',
				'options' => [
					'route' => '/users[/:id][/]',
					'constraints' => [
						'id' => '[0-9]+',
					],
					'defaults' => [
						'controller' => 'Aeris\ZendRestModuleTest\RestTestModule\Controller\UserRest'
					],
				]
			],

			'users-array' => [
				'type' => 'literal',
				'options' => [
					'route' => '/users/as-array',
					'defaults' => [
						'controller' => 'Aeris\ZendRestModuleTest\RestTestModule\Controller\UserRest',
						'action' => 'getListAsArray',
					]
				]
			],

			'animals' => [
				'type' => 'Aeris\ZendRestModule\Mvc\Router\Http\RestSegment',
				'options' => [
					'route' => '/animals[/:id][/]',
					'constraints' => array(
						'id' => '[0-9]+',
					),
					'defaults'    => array(
						'controller' => 'Aeris\ZendRestModuleTest\RestTestModule\Controller\AnimalRest'
					),
				]
			],

			// TestedRestSegment
			'animals-rest-tested-success' => [
				'type' => 'Aeris\ZendRestModule\Mvc\Router\Http\TestedRestSegment',
				'options' => [
					'route' => '/animals/rest-tested/success[/:id][/]',
					'constraints' => [
						'id' => '[0-9]+',
					],
					'defaults' => [
						'controller' => 'Aeris\ZendRestModuleTest\RestTestModule\Controller\AnimalRest',
						'test' => function (RequestInterface $request, ServiceManager $serviceManager) {
							$config = $serviceManager->get('config');

							return $config['paramsForTestedRoute']['animalType'] === 'monkey';
						}
					],
				]
			],
			'animals-rest-tested-fail' => [
				'type' => 'Aeris\ZendRestModule\Mvc\Router\Http\TestedRestSegment',
				'options' => [
					'route' => '/animals/rest-tested/fail[/:id][/]',
					'constraints' => [
						'id' => '[0-9]+',
					],
					'defaults' => [
						'controller' => 'Aeris\ZendRestModuleTest\RestTestModule\Controller\AnimalRest',
						'test' => function (RequestInterface $request, ServiceManager $serviceManager) {
							$config = $serviceManager->get('config');

							return $config['paramsForTestedRoute']['animalType'] === 'kudu';
						}
					],
				]
			],


			// TestedLiteral
			'animals-literal-tested-success' => [
				'type' => 'Aeris\ZendRestModule\Mvc\Router\Http\TestedLiteral',
				'options' => [
					'route' => '/animals/literal-tested/success',
					'defaults'    => [
						'controller' => 'Aeris\ZendRestModuleTest\RestTestModule\Controller\Animal',
						'action' => 'monkey',
						'test' => function(RequestInterface $request, ServiceManager $serviceManager) {
							$config = $serviceManager->get('config');

							return $config['paramsForTestedRoute']['animalType'] === 'monkey';
						}
					],
				]
			],
			'animals-literal-tested-fail' => [
				'type' => 'Aeris\ZendRestModule\Mvc\Router\Http\TestedLiteral',
				'options' => [
					'route' => '/animals/literal-tested/fail',
					'defaults'    => [
						'controller' => 'Aeris\ZendRestModuleTest\RestTestModule\Controller\Animal',
						'action' => 'monkey',
						'test' => function(RequestInterface $request, ServiceManager $serviceManager) {
							$config = $serviceManager->get('config');

							return $config['paramsForTestedRoute']['animalType'] === 'kudu';
						}
					],
				]
			],

			'animals-throw-for-modifying-view-model' => [
				'type' => 'literal',
				'options' => [
					'route' => '/animals/alter-error-response-object',
					'defaults' => [
						'controller' => 'Aeris\ZendRestModuleTest\RestTestModule\Controller\Animal',
						'action' => 'throwModifyViewModelException'
					]
				]
			]
		],
	],
	'serializationContexts' => [
		'Aeris\ZendRestModuleTest\RestTestModule\Controller\UserRest' => [
			'get' => [
				'groups' => ['userDetails']
			],
			'getList' => [
				'groups' => ['userSummary']
			],
		],
	],
];
