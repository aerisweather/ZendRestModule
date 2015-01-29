<?php

namespace Aeris\ZendRestModule\Service\Serializer;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer as JMSSerializer;
use JMS\Serializer\SerializerBuilder;

class Serializer implements SerializerInterface
{

	/**
	 * @var JMSSerializer
	 */
	protected $serializer;

	public function __construct(array $config) {
		$serializerBuilder = SerializerBuilder::create();
		if (isset($config['cacheDir'])) {
			$serializerBuilder->setCacheDir($config['cacheDir']);
		}

		if (isset($config['propertyNamingStrategy'])) {
			$serializerBuilder->setPropertyNamingStrategy($config['propertyNamingStrategy']);
		}

		if (isset($config['objectConstructor'])) {
			$serializerBuilder->setObjectConstructor($config['objectConstructor']);
		}

		if (isset($config['debug'])) {
			$serializerBuilder->setDebug((bool)$config['objectConstructor']);
		}

		if(isset($config['extraHandlers'])) {
			$extraHandlers = $config['extraHandlers'];
			$serializerBuilder->addDefaultHandlers();

			foreach($config['extraHandlers'] as $handler) {
				if(!$handler instanceof SubscribingHandlerInterface) {
					throw new \RuntimeException('Handler '.get_class($handler).' wasn\'t an instance of SubscribingHandlerInterface');
				}
			}
			$serializerBuilder->configureHandlers(function(HandlerRegistry $handlerRegistry) use ($extraHandlers) {
				foreach($extraHandlers as $handler) {
					$handlerRegistry->registerSubscribingHandler($handler);
				}
			});
		}

		if (isset($config['subscribers'])) {
			$subscribers = $config['subscribers'];
			$serializerBuilder->configureListeners(function(EventDispatcher $dispatcher) use ($subscribers) {
				array_walk($subscribers, [$dispatcher, 'addSubscriber']);
			});
		}

		$this->serializer = $serializerBuilder->build();
	}

	public function serialize($data, $format, SerializationContext $context = null) {
		return $this->serializer->serialize($data, $format, $context);
	}

	/**
	 * Deserialize
	 *
	 * JMS Deserialized has been wrapped to allow looser typing of arguments.
	 * @param string|array $data
	 * @param string|\StdClass $object
	 * @param string $format
	 * @return mixed
	 */
	public function deserialize($data, $object, $format = 'json') {
		if(is_array($data)) {
			$data = json_encode($data);
		}

		if(is_object($object)) {
			$context = new DeserializationContext();
			$context->attributes->set('target', $object);

			return $this->serializer->deserialize($data, get_class($object), $format, $context);
		}
		else {
			return $this->serializer->deserialize($data, $object, $format);
		}
	}
}
