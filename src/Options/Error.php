<?php


namespace Aeris\ZendRestModule\Options;


use Zend\Stdlib\AbstractOptions;

class Error extends AbstractOptions {
	/**
	 * The error/exception which will trigger the configured
	 * JSON error response.
	 *
	 * Exception class name,
	 * or error constant (eg. Zend\Mvc\Application::ERROR_EXCEPTION)
	 *
	 * @var string
	 */
	private $error = '\Exception';

	/**
	 * Http code to use in the error response
	 *
	 * @var int
	 */
	private $httpCode = 500;

	/**
	 * Passed in the JSON response object as `error.code`
	 *
	 * @var string
	 */
	private $applicationCode = 'unknown_error';

	/**
	 * Passed in the JSON response object as `error.details`
	 *
	 * @var string
	 */
	private $details = 'An unknown error occurred.';

	/**
	 * Callback triggered after the error is caught,
	 * but before a response is returned to the user.
	 *
	 * Receives a \Aeris\ZendRestModule\Event\RestErrorEvent
	 * argument.
	 *
	 * Can be used to log errors,
	 * or to modify the the JSON error response.
	 *
	 * eg.:
	 * 	function(RestErrorEvt $evt) {
	 * 		error_log($evt->getError());
	 *
	 * 		$viewModel = $evt->getViewModel();
	 * 		$errorObj = $viewModel->getVariable('error');
	 * 		$errorObj['foo'] = 'bar';
	 * 		$viewModel->setVariable('error', $errorObj);
	 *  }
	 *
	 *
	 * @var callable
	 */
	private $onError = null;

	public function __construct($options = null) {
		parent::__construct($options);

		if (is_null($this->getOnError())) {
			$this->setOnError(function() {});
		}
	}

	public function toArray() {
		return [
			'error' => $this->error,
			'httpCode' => $this->httpCode,
			'applicationCode' => $this->applicationCode,
			'details' => $this->details,
			'onError' => $this->onError
		];
	}

	/**
	 * @return string
	 */
	public function getError() {
		return $this->error;
	}

	/**
	 * @param string $error
	 */
	public function setError($error) {
		$this->error = $error;
	}

	/**
	 * @return int
	 */
	public function getHttpCode() {
		return $this->httpCode;
	}

	/**
	 * @param int $httpCode
	 */
	public function setHttpCode($httpCode) {
		$this->httpCode = $httpCode;
	}

	/**
	 * @return string
	 */
	public function getApplicationCode() {
		return $this->applicationCode;
	}

	/**
	 * @param string $applicationCode
	 */
	public function setApplicationCode($applicationCode) {
		$this->applicationCode = $applicationCode;
	}

	/**
	 * @return callable
	 */
	public function getOnError() {
		return $this->onError;
	}

	/**
	 * @param callable $onError
	 */
	public function setOnError($onError) {
		$this->onError = $onError;
	}

	/**
	 * @return string
	 */
	public function getDetails() {
		return $this->details;
	}

	/**
	 * @param string $details
	 */
	public function setDetails($details) {
		$this->details = $details;
	}
}