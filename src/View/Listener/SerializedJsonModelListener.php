<?php
namespace Aeris\ZendRestModule\View\Listener;

use Aeris\ZendRestModule\Options\ZendRest;
use Aeris\ZendRestModule\View\Model\SerializedJsonModel;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Model\ViewModel;
use Zend\EventManager\EventManagerInterface as Events;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\Mvc\MvcEvent;

class SerializedJsonModelListener extends AbstractListenerAggregate {

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
		/** @var ServiceManager $serviceManger */
		$serviceManger = $evt->getApplication()
			->getServiceManager();

		/** @var SerializedJsonModel $serializedJsonModel */
		$serializedJsonModel = $serviceManger
			->create('Aeris\ZendRestModule\View\Model\SerializedJsonModel');

		$groups = $this->getSerializationGroups($evt);

		if ($groups) {
			$serializedJsonModel->setSerializationGroups($groups);
		}

		return $serializedJsonModel;
	}

	/**
	 * @param MvcEvent $evt
	 * @return string[]
	 */
	private function getSerializationGroups(MvcEvent $evt) {
		/** @var ZendRest $zendRestOptions */
		$zendRestOptions = $evt
			->getApplication()
			->getServiceManager()
			->get('Aeris\ZendRestModule\Options\ZendRest');

		$controllerName = $evt->getRouteMatch()->getParam('controller');
		$actionName = $evt->getRouteMatch()->getParam('action');

		return $zendRestOptions->getSerializationGroups($controllerName, $actionName);
	}

}
