<?php


namespace Aeris\ZendRestModule\Event;


use Zend\EventManager\Event;
use Zend\View\Model\ViewModel;

class RestErrorEvent extends Event {

	public function __construct(array $params){
		parent::__construct('rest-error-event', null, []);

		// Set params manually, so we can validate and stuff.
		$setParamUsing = function($methodName, $value) use ($params) {
			if (isset($params[$value])) {
				call_user_func([$this, $methodName], $params[$value]);
			}
		};

		$setParamUsing('setError', 'error');
		$setParamUsing('setErrorConfig', 'errorConfig');
		$setParamUsing('setViewModel', 'viewModel');
	}

	/**
	 * @param \Exception|string $error
	 */
	protected function setError($error) {
		$this->setParam('error', $error);
	}

	/**
	 * @return \Exception|string
	 */
	public function getError() {
		return $this->getParam('error');
	}

	/**
	 * @param array $errorConfig
	 */
	protected function setErrorConfig(array $errorConfig) {
		$this->setParam('errorConfig', $errorConfig);
	}

	/**
	 * @return array
	 */
	public function getErrorConfig(){
		return $this->getParam('errorConfig');
	}

	/**
	 * @param ViewModel $viewModel
	 */
	protected function setViewModel(ViewModel $viewModel) {
		$this->setParam('viewModel', $viewModel);
	}

	/**
	 * @return ViewModel
	 */
	public function getViewModel() {
		return $this->getParam('viewModel');
	}

}
