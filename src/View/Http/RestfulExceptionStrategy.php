<?php

namespace Aeris\ZendRestModule\View\Http;


use Aeris\ZendRestModule\Event\RestErrorEvent;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\ResponseInterface;
use Zend\View\Model\JsonModel;


/**
 * Handles exceptions thrown by controllers,
 * and converts the exceptions into JSON error objects.
 *
 * Class RestfulExceptionStrategy
 * @package Aeris\ZendRestModule\View\Http
 */
class RestfulExceptionStrategy extends AbstractListenerAggregate
	implements EventManagerAwareInterface {

	/**
	 * Configure how \Exception objects
	 * are converted into JSON error object.
	 *
	 * eg.
	 *
	 *    array(
	 *      array(
	 *        'error' => Application::ERROR_ROUTER_NO_MATCH,
	 *        'httpCode' => 404,
	 *        'applicationCode' => 'invalid_endpoint',
	 *        'details' => 'The requested endpoint is not valid',
	 *      ),
	 *      array(
	 *        'error' => '\MyApp\Exceptions\ReallyBadException',
	 *        'httpCode' => 418,
	 *        'applicationCode' => 'really_bad',
	 *        'details' => 'You did something really bad. For shame...',
	 *      ),
	 *    );
	 *
	 * @var array
	 */
	protected $errorObjectConfig;

	/** @var EventManagerInterface */
	private $eventManager;


	public function __construct($errorObjectConfig = array()) {
		$this->errorObjectConfig = $errorObjectConfig;
	}


	public function attach(EventManagerInterface $events) {
		$this->listeners[] = $events->attach(
			MvcEvent::EVENT_DISPATCH_ERROR,
			array($this, 'prepareExceptionViewModel'),
			-10
		);
		$this->listeners[] = $events->attach(
			MvcEvent::EVENT_RENDER_ERROR,
			array($this, 'prepareExceptionViewModel'),
			-10
		);

		// Call configured onError callbacks
		$this->getEventManager()
			->getSharedManager()
			->attach('Aeris\ZendRestModule\RestException',
				'exception',
				function (RestErrorEvent $evt) {
					if (isset($evt->getErrorConfig()['on_error'])) {
						$onError = $evt->getErrorConfig()['on_error'];
						$onError($evt);
					}
				});
	}

	/**
	 * @return array
	 */
	public function getErrorObjectConfig() {
		return $this->errorObjectConfig;
	}

	/**
	 * @param array $errorObjectConfig
	 */
	public function setErrorObjectConfig($errorObjectConfig) {
		$this->errorObjectConfig = $errorObjectConfig;
	}


	/**
	 * Updates the event result with an error object (SerializedJsonModel),
	 *
	 * @param MvcEvent $evt
	 */
	public function prepareExceptionViewModel(MvcEvent $evt) {
		$errorName = $evt->getError();

		if (empty($errorName)) {
			return;
		};

		$errorObj = $evt->getResult()->getVariable('exception');

		$this->updateEventWithError($evt, $errorObj, $errorName);
	}


	protected function updateEventWithError(MvcEvent $evt, \Exception $errorObj = null, $errorName = '') {
		// Do nothing if the result is a response object
		$result = $evt->getResult();
		if ($result instanceof ResponseInterface) {
			return;
		}

		$errorConfig = $this->getConfigForError($errorObj, $errorName);

		$viewModel = $this->createViewModel($evt, array(
			'error' => array(
				'code' => $errorConfig['application_code'],
				'details' => is_callable($errorConfig['details']) ?
					$errorConfig['details']($errorObj, $errorName) :
					$errorConfig['details'],
			)
		));
		$evt->setResult($viewModel);

		$evt->getResponse()
			->setStatusCode($errorConfig['http_code']);


		$this->getEventManager()
			->trigger('exception', null, new RestErrorEvent([
				'error' => $errorObj,
				'errorConfig' => $errorConfig,
				'viewModel' => $viewModel
			]));
	}


	/**
	 * @param \Exception Exception|null $errorObject
	 * @param string $errorString
	 */
	protected function getConfigForError(\Exception $errorObject = null, $errorString = '') {
		$defaultConfig = array(
			'error' => '\Exception',
			'http_code' => 500,
			'application_code' => 'unknown_error',
			'details' => 'Unknown application error.'
		);
		$errorObjectConfig = $this->getErrorObjectConfig();

		$matchingConfigs = array_filter($errorObjectConfig,
			function ($config) use ($errorObject, $errorString) {
				return is_a($errorObject, $config['error']) ||
				$errorString === $config['error'];
			});

		$matchingConfigs = array_values($matchingConfigs);
		return $matchingConfigs ?
			array_merge($defaultConfig, $matchingConfigs[0]) : $defaultConfig;
	}


	protected function createViewModel(MvcEvent $evt, $model) {
		return new JsonModel($model);
	}

	/**
	 * Inject an EventManager instance
	 *
	 * @param  EventManagerInterface $eventManager
	 * @return void
	 */
	public function setEventManager(EventManagerInterface $eventManager) {
		$eventManager->addIdentifiers([
			'Aeris\ZendRestModule\RestException'
		]);

		$this->eventManager = $eventManager;
	}

	/**
	 * Retrieve the event manager
	 *
	 * Lazy-loads an EventManager instance if none registered.
	 *
	 * @return EventManagerInterface
	 */
	public function getEventManager() {
		return $this->eventManager;
	}
}
