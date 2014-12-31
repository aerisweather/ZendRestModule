<?php
namespace Aeris\ZendRestModule\View\Listener;

use Aeris\ZendRestModule\View\Model\SerializedJsonModel;
use Zend\View\Model\ViewModel;
use Zend\EventManager\EventManagerInterface as Events;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\Mvc\MvcEvent;

class CreateSerializedJsonViewModelListener extends AbstractListenerAggregate {

	public function attach(Events $events) {
		$this->listeners[] = $events->attach(
			MvcEvent::EVENT_RENDER,
			array($this, 'updateViewModelFromResult')
		);
	}

	public function updateViewModelFromResult(MvcEvent $e) {
		$result = $e->getResult();

		if ($result instanceof ViewModel) {
			$viewModel = $result;
		}
		else {
			$viewModel = $this->createViewModel($e);
			$viewModel->setModel($result);
		}

		$e->setViewModel($viewModel);
	}


	protected function createViewModel(MvcEvent $evt) {
		/** @var SerializedJsonModel $serializedJsonModel */
		$serializedJsonModel = $evt->getApplication()
			->getServiceManager()
			->get('SerializedJsonModel');

		$context = $this->getSerializationContext($evt);

		if ($context) {
			$serializedJsonModel->setContext($context);
		}

		return $serializedJsonModel;
	}

	/**
	 * @param MvcEvent $evt
	 * @return array
	 */
	private function getSerializationContext(MvcEvent $evt) {
		$config = $evt
			->getApplication()
			->getServiceManager()
			->get('config');
		$serializationConfig = $config['serializationContexts'];

		$controllerName = $evt->getRouteMatch()->getParam('controller');
		$actionName = $evt->getRouteMatch()->getParam('action');

		$context = @$serializationConfig[$controllerName][$actionName]['groups'];

		return $context;
	}

}
