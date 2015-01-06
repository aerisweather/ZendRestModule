<?php
/**
 * Created by PhpStorm.
 * User: edanschwartz
 * Date: 7/29/14
 * Time: 2:48 PM
 */

namespace Aeris\ZendRestModule\View\Model;


use Zend\View\Model\JsonModel;
use Aeris\ZendRestModule\Service\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;

/**
 * Uses the
 */
class SerializedJsonModel extends JsonModel {

	/**
	 * @var SerializerInterface
	 */
	protected $serializer;

	/**
	 * @var SerializationContext
	 */
	protected $context = null;

	/**
	 * @param object|null $model The model to serialize.
	 * @param array|\Traversable|null $options
	 */
	public function __construct($model = null, $options = null) {
		parent::__construct(array(
			'model' => $model
		), $options);
	}

	/**
	 * @param array $contexts
	 * @return $this
	 */
	public function setSerializationGroups($groups) {
		$this->context->setGroups($groups);

		return $this;
	}


	public function serialize() {
		$object = $this->getVariables()['model'];

		if (is_null($object)) {
			return null;
		}

		return $this->getSerializer()
			->serialize($object, 'json', $this->context);
	}


	/**
	 * @return SerializerInterface
	 */
	public function getSerializer() {
		return $this->serializer;
	}

	public function setSerializer(SerializerInterface $serializer) {
		$this->serializer = $serializer;
	}


	public function setModel($model) {
		$this->setVariable('model', $model);
	}

	public function getModel() {
		return $this->getVariable('model');
	}


	/**
	 * @return SerializationContext
	 */
	public function getContext() {
		return $this->context;
	}

	/**
	 * @param SerializationContext $context
	 */
	public function setContext($context) {
		$this->context = $context;
	}
}
