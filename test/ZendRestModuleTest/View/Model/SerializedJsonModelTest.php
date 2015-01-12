<?php
/**
 * Created by PhpStorm.
 * User: edanschwartz
 * Date: 7/29/14
 * Time: 2:57 PM
 */

namespace Aeris\ZendRestModuleTest\View\Model;

use Aeris\ZendRestModule\View\Model\SerializedJsonModel;
use JMS\Serializer\SerializationContext;

class SerializedJsonModelTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject
	 */
	protected $serializerMock;

	public function setUp() {
		$this->serializerMock = $this->getJmsSerializerMock();
	}


	/**
	 * @test
	 */
	public function shouldSerializeModelToJsonUsingJMSSerializer() {
		$object = new \stdClass();
		$viewModel = new SerializedJsonModel($object);
		$viewModel->setSerializer($this->serializerMock);
		$viewModel->setContext(SerializationContext::create());

		$serializedJson = json_encode(array('foo' => 'bar' ));

		$this->serializerMock
			->expects($this->once())
			->method('serialize')
			->with($object, 'json')
			->will($this->returnValue($serializedJson));

		$this->assertEquals($serializedJson, $viewModel->serialize());
	}


	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject
	 */
	protected function getJmsSerializerMock() {
		return $this->getMockBuilder('Aeris\ZendRestModule\Service\Serializer\SerializerInterface')
			->getMock();
	}

}
