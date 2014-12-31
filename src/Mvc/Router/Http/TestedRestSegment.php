<?php


namespace Aeris\ZendRestModule\Mvc\Router\Http;


use Aeris\ZendRestModule\Mvc\Router\Exception\InvalidArgumentException;
use Zend\Mvc\Router\RoutePluginManager;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\Stdlib\RequestInterface;

/**
 * Accepts a `test` configuration,
 * which can be used to determine whether the route is valid.
 *
 * Example:
 *
 * 	'tested-route' => [
 *  	'type' => 'Aeris\ZendRestModule\Mvc\Router\Http\TestedRestSegment',
 * 		'options' => [
 * 			'route' => '/myResource[/:id][/]',
 * 			'defaults' => [
 * 				'controller' => 'Controller\MyResourceRest',
 * 				'test' => function(RequestInterface $request, ServiceManager $serviceManager) {
 * 					$superSecretKey = $serviceManager->get('config')['superSecretKey'];
 *
 * 					return $superSecretKey == 42;
 * 				}
 * 			]
 * 		]
 *  ]
 */
class TestedRestSegment extends RestSegment implements ServiceLocatorAwareInterface {
	use ServiceLocatorAwareTrait;

	public static function factory($options = []) {
		$obj = parent::factory($options);

		$hasTest = isset($options['defaults']['test']) &&
			is_callable($options['defaults']['test']);

		if (!$hasTest) {
			throw new InvalidArgumentException('TestRestSegment routes must define a `test` callable');
		}

		return $obj;
	}

	public function match(RequestInterface $request, $pathOffset = null, array $options = array()) {
		$routeMatch = parent::match($request, $pathOffset, $options);

		/** @var callable $test */
		$test = $this->defaults['test'];
		$result = $test($request, $this->getServiceManager());

		return $result ? $routeMatch : null;
	}

	public function getServiceManager() {
		/** @var RoutePluginManager $routePluginManager */
		$routePluginManager = $this->getServiceLocator();

		return $routePluginManager->getServiceLocator();
	}
}
