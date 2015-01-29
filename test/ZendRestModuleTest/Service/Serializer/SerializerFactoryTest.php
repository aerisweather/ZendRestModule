<?php


namespace Aeris\ZendRestModuleTest\Service\Serializer;


use Aeris\ZendRestModule\Options\ZendRest as ZendRestOptions;
use Aeris\ZendRestModule\Service\Serializer\SerializerFactory;
use ZendTest\Loader\TestAsset\ServiceLocator;
use Aeris\ZendRestModule\Options\Serializer as SerializerOptions;
use \Mockery as M;

class SerializerFactoryTest extends \PHPUnit_Framework_TestCase {

	/** @var ServiceLocator */
	protected $serviceLocator;

	/** @var SerializerOptions */
	protected $serializerOptions;


	public function setUp() {
		$this->serviceLocator = new ServiceLocator();


		$this->serializerOptions = new SerializerOptions([
			'handlers' => [],
			// use default constructor to limit test scope
			'objectConstructor' => '\JMS\Serializer\Construction\UnserializeObjectConstructor',
			// use default naming strategy
			'namingStrategy' => '\JMS\Serializer\Naming\IdenticalPropertyNamingStrategy',
		]);
		$zendRestOptions = new ZendRestOptions();
		$zendRestOptions->setSerializer($this->serializerOptions);
		$this->serviceLocator->set('Aeris\ZendRestModule\Options\ZendRest', $zendRestOptions);
	}

	/** @test */
	public function shouldRegisterSubscribers() {
		$subscriberMock = $this->registerSubscriberMock('EventSubscriberMock');

		$this->serializerOptions
			->setSubscribers([
				'EventSubscriberMock'
			]);

		$serializerFactory = new SerializerFactory();
		$serializer = $serializerFactory->createService($this->serviceLocator);

		$serializer->serialize(new \stdClass(), 'json');

		$subscriberMock
			->shouldHaveReceived('onPreSerialize')
			->once();
		$subscriberMock
			->shouldHaveReceived('onPostSerialize')
			->once();

		$serializer->deserialize(json_encode(['foo' => 'bar']), new \stdClass());

		$subscriberMock
			->shouldHaveReceived('onPreDeserialize')
			->once();

		$subscriberMock->shouldHaveReceived('onPostDeserialize')
			->once();
	}


	/** @test */
	public function shouldRegisterListeners() {
		$subscriberMockA = $this->registerSubscriberMock('EventSubscriberMockA');
		$subscriberMockB = $this->registerSubscriberMock('EventSubscriberMockB');

		$this->serializerOptions
			->setListeners([
				'serializer.pre_serialize' => [
					[$subscriberMockA, 'onPreSerialize'],
					[$subscriberMockB, 'onPreSerialize']
				],
				'serializer.post_serialize' => [
					[$subscriberMockA, 'onPostSerialize'],
					[$subscriberMockB, 'onPostSerialize'],
				]
			]);

		$serializerFactory = new SerializerFactory();
		$serializer = $serializerFactory->createService($this->serviceLocator);

		$serializer->serialize(new \stdClass(), 'json');
		
		$subscriberMockA
			->shouldHaveReceived('onPreSerialize')
			->once();
		$subscriberMockB
			->shouldHaveReceived('onPreSerialize')
			->once();
		
		$subscriberMockA
			->shouldHaveReceived('onPostSerialize')
			->once();
		$subscriberMockB
			->shouldHaveReceived('onPostSerialize')
			->once();
	}

	/**
	 * Create a mock EventSubscriberInterface,
	 * and register it with serviceLocator.
	 *
	 * Comes with callbacks:
	 * - onPreSerialize
	 * - onPostSerialize
	 * - onPreDeserialize
	 * - onPostDeserialize
	 *
	 * @param string $name service name
	 * @return M\MockInterface
	 */
	protected function registerSubscriberMock($name) {
		return $this->registerServiceMock($name, '\JMS\Serializer\EventDispatcher\EventSubscriberInterface', [
			'getSubscribedEvents' => [
				[
					'event' => 'serializer.pre_serialize',
					'method' => 'onPreSerialize'
				],
				[
					'event' => 'serializer.post_serialize',
					'method' => 'onPostSerialize'
				],
				[
					'event' => 'serializer.pre_deserialize',
					'method' => 'onPreDeserialize'
				],
				[
					'event' => 'serializer.post_deserialize',
					'method' => 'onPostDeserialize'
				],
			],
			'onPreSerialize' => null,
			'onPostSerialize' => null,
			'onPreDeserialize' => null,
			'onPostDeserialize' => null,
		]);
	}

	protected function registerServiceMock($name, $className = '\stdClass', $methods = null) {
		$mock = M::mock($className, $methods);

		$this->serviceLocator->set($name, $mock);

		return $mock;
	}

}