<?php


namespace Aeris\ZendRestModuleTest;

use Aeris\GuzzleHttpMock\Mock as HttpMock;
use Zend\Stdlib\ArrayUtils;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase as BaseTestCase;
use \Mockery as M;

class AbstractTestCase extends BaseTestCase
{

	/**
	 * @var string Location of the app configuration.
	 * 						 Expected to contain an 'application.config.php'
	 * 						 and an '/autoload' directory.
	 */
	protected $appConfigDir = '';

	/** @var array */
	private $serviceMocks = [];

	/** @var callable[] */
	private $serviceMockFactories = [];

	/** @var HttpMock[] */
	private $httpMocks = [];

	protected function setUp() {
		parent::setUp();
		$this->bootApplication();
	}

	protected function bootApplication() {
		$this->setApplicationConfig($this->getApplicationTestConfig());

		// Restore all service mocks
		foreach ($this->serviceMocks as $name => &$service) {
			$this->useServiceMock($name, $service);
		}
		foreach ($this->serviceMockFactories as $name => $mockFactory) {
			$this->useServiceMockFactory($name, $mockFactory);
		}
	}

	/**
	 * Restart the application.
	 */
	protected function rebootApplication() {
		$this->reset();
		$this->bootApplication();
	}

	/**
	 * This will clear out any Requests from memory.
	 *
	 * If you need to dispatch multiple requests in a single test,
	 * you should run this method before each subsequent `dispatch`.
	 * This will reset your expectedRequest object, so you do not get info from
	 * previous requests all merged into a single object.
	 */
	protected function resetResponseObject() {
		// Rebooting the application will clear out the expectedRequest object.
		$this->rebootApplication();
	}

	protected function tearDown() {
		foreach ($this->httpMocks as $mock) {
			$mock->verify();
		}

		parent::tearDown();
		M::close();
	}

	/**
	 * Dispatch
	 *
	 * This is unfortunately necessary because PHPUnit doesn't set all of the server headers
	 * needed by the OAuth2 module.
	 *
	 * @param string $url
	 * @param null $method
	 * @param array $params
	 * @param bool $isXmlHttpRequest
	 */
	public function dispatch($url, $method = null, $params = array(), $isXmlHttpRequest = false) {
		$methodOrig                = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : null;
		$_SERVER['REQUEST_METHOD'] = $method;

		parent::dispatch($url, $method, $params, $isXmlHttpRequest);

		if ($methodOrig) {
			$_SERVER['REQUEST_METHOD'] = $methodOrig;
		}
		else {
			unset($_SERVER['REQUEST_METHOD']);
		}
	}

	/**
	 * Dispatch JSON
	 *
	 * Dispatch a JSON expectedRequest, better simulates json deserialization in the app. Also nested arrays aren't handled very
	 * well with Zend's built in dispatch method.
	 *
	 * @param string $url
	 * @param null $method
	 * @param array $params
	 * @param bool $isXmlHttpRequest
	 */
	public function dispatchJson($url, $method = null, $params = array(), $isXmlHttpRequest = false) {
		/** @var \Zend\Http\Request $request */
		$request = $this->getRequest();
		$headers = $request->getHeaders();
		$headers->addHeaderLine('Content-Type', 'application/json');

		$request->setContent(json_encode($params));

		$this->dispatch($url, $method, array(), $isXmlHttpRequest);
	}

	/**
	 * Override a service manager service
	 * with the provided mock object.
	 *
	 * Note that this will not work with
	 * ServiceManager::create(), as there is no method
	 * defined for creating a new instance. Use usesServiceMockFactory() instead.
	 *
	 * @param string $serviceName
	 * @param object $serviceMock
	 * @param boolean $mockAfterReboot
	 * 			  If set to false, service mock will be reset
	 * 				when the application is rebooted (eg, after a second expectedRequest is dispatched)
	 */
	protected function useServiceMock($serviceName, &$serviceMock, $mockAfterReboot = true) {
		$sm = $this->getApplicationServiceLocator();
		$sm->setAllowOverride(true);
		$sm->setService($serviceName, $serviceMock);

		if ($mockAfterReboot) {
			$this->serviceMocks[$serviceName] = $serviceMock;
		}
	}

	/**
	 * Override a service manager service
	 * with a mock factory.
	 *
	 * This is useful if you need to use ServiceManager::create(),
	 * to create a "new" instance of your mock. Note that intializers
	 * will be applied to the created mock object.
	 *
	 * For a simpler alternative, use useServiceMock();
	 *
	 * @param          $serviceName
	 * @param callable $serviceFactory
	 * @param bool     $mockAfterReboot
	 */
	protected function useServiceMockFactory($serviceName, callable $serviceFactory, $mockAfterReboot = true) {
		$sm = $this->getApplicationServiceLocator();
		$sm->setAllowOverride(true);
		$sm->setFactory($serviceName, $serviceFactory);

		if ($mockAfterReboot) {
			$this->serviceMockFactories[$serviceName] = $serviceFactory;
		}
	}

	/**
	 * Use an existing service within the test case.
	 * Will ensure that the same service object instance
	 * is used, even when the application is rebooted.
	 *
	 * @param string $serviceName
	 * @return array|object
	 */
	protected function useService($serviceName) {
		$service = $this->getApplicationServiceLocator()
			->get($serviceName);

		// Remember the service instance, for application reboot.
		$this->useServiceMock($serviceName, $service);

		return $service;
	}

	/**
	 * Creates an HttpMock,
	 * which will automatically be verified on teardown.
	 *
	 * @return HttpMock
	 */
	protected function createHttpMock() {
		$mock = new HttpMock();
		$this->httpMocks[] = $mock;

		return $mock;
	}


	protected function getApplicationTestConfig() {
		if (!is_dir($this->appConfigDir)) {
			throw new \Exception("$this->appConfigDir is not a valid app config directory.");
		}

		$appConfig    = include $this->appConfigDir . '/application.config.php';

		return ArrayUtils::merge($appConfig, array(
			'module_listener_options' => array(
				'config_glob_paths' => array(
					$this->appConfigDir . '/autoload/{,*.}{global,local,test}.php'
				),
			),
		));
	}

	/** @returns array */
	protected function getJsonResponse() {
		return json_decode($this->getResponse()
								->getContent(), $assoc = true);
	}

	/**
	 * Assert Route Match
	 *
	 * Asserts that the dispatched method is actually routed to the expected location.
	 * @param $moduleName
	 * @param $controllerName
	 * @param $action
	 */
	public function assertRouteMatch($moduleName, $controllerName, $action) {
		$this->assertModuleName($moduleName);
		$this->assertControllerClass($controllerName);
		$this->assertActionName($action);
	}

	/**
	 * @param array|string $expectedResponseData Expected response data array, or json string.
	 */
	protected function assertJsonResponseEquals($expectedResponseData) {
		$expectedJsonResponse = is_array($expectedResponseData) ? json_encode($expectedResponseData) : $expectedResponseData;

		$responseContent = $this->getResponse()
								->getContent();

		$this->assertJsonStringEqualsJsonString($expectedJsonResponse, $responseContent, 'JSON response is not as expected.');
	}


	protected function assertJsonResponseContains($expectedResponse_partial) {
		$expectedPartialResponse = is_string($expectedResponse_partial) ? json_decode($expectedResponse_partial, true) : $expectedResponse_partial;

		$this->assertArrayContains($expectedPartialResponse, $this->getJsonResponse());
	}

	/**
	 * @param $expectedPartialArray
	 * @param $actualArray
	 */
	protected function assertArrayContains($expectedPartialArray, $actualArray) {
		foreach ($expectedPartialArray as $key => $val) {
			if (is_array($val)) {
				//Array passed, we expect multiple responses.
				$this->assertInternalType('array', $actualArray);

				$keyValueFound = false;
				foreach ($val as $testKey => $testVal) {
					foreach ($actualArray as $actualKey => $actualValue) {
						if (isset($actualValue[$testKey]) && $actualValue[$testKey] == $testVal) {
							$keyValueFound = true;
							break;
						}
						else {
							$keyValueFound = false;
						}
					}
					$this->assertTrue($keyValueFound, 'Couldn\'t find ' . $testKey . ' => ' . $testVal . ' in response');
				}
			}
			else {
				//Plain key =>  value pair
				$this->assertArrayHasKey($key, $actualArray);

				$actualValue = $actualArray[$key];

				$this->assertEquals($val, $actualValue);
			}
		}
	}


	protected function assertJsonErrorCodeEquals($errorCode) {
		$jsonData = json_decode($this->getResponse()
									 ->getContent(), true);

		$this->assertArrayHasKey('error', $jsonData, 'Failed asserting json error response.');
		$this->assertEquals($errorCode, $jsonData['error']['code'], 'Incorrect JSON error code.');
	}

	protected function assertClientAuthorizationErrorResponse() {
		$this->assertResponseStatusCode(401);
		$this->assertJsonErrorCodeEquals('unauthorized_client');
	}

	protected function assertUserAuthorizationErrorResponse(){
		$this->assertResponseStatusCode(401);
		$this->assertJsonErrorCodeEquals('unauthorized_user');
	}

	protected function assertNotFoundErrorResponse() {
		$this->assertNotFoundErrorReponse();
	}

	/**
	 * @deprecated (typo in method name)
	 */
	protected function assertNotFoundErrorReponse() {
		$this->assertResponseStatusCode(404);
		$this->assertJsonErrorCodeEquals('warn_no_data');
	}

	protected function assertInvalidRequestResponse() {
		$this->assertResponseStatusCode(404);
		$this->assertJsonErrorCodeEquals('invalid_request');
	}

	protected function assertEmptyResponse() {
		$this->assertResponseStatusCode(204);
		$this->assertEquals('', $this->getResponse()
									 ->getContent());
	}

	protected function assertValidationErrorResponse() {
		$this->assertResponseStatusCode(400);
		$this->assertJsonErrorCodeEquals('validation_error');
	}
}
