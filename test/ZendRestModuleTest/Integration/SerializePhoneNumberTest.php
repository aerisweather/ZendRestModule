<?php


namespace Aeris\ZendRestModuleTest\Integration;


use Aeris\ZendRestModuleTest\RestTestModule\Model\BirthdayBoy;
use Aeris\ZendRestModuleTest\RestTestModule\Model\CellPhone;
use JMS\Serializer\SerializerInterface;

class SerializePhoneNumberTest extends IntegrationTestCase {

	/** @var SerializerInterface */
	protected $serializer;

	public function setUp() {
		parent::setUp();

		$this->serializer = $this
			->getApplicationServiceLocator()
			->get('Aeris\ZendRestModule\Serializer');
	}

	/** @test */
	public function shouldSerializePhoneNumber() {
		$cellPhone = new CellPhone();
		$cellPhone->setPhoneNumber("16125551234x567");

		$json = $this->serializer->serialize($cellPhone, 'json');

		$this->assertJsonStringEqualsJsonString($json, '{"cellPhone": "" }');
	}

	/** @test */
	public function shouldSerializeDateTimesAsTimestamps() {
		$birthdayBoy = new BirthdayBoy();
		$birthdayBoy->setBirthDate(self::FromTimestamp(1234567));

		$json = $this->serializer->serialize($birthdayBoy, 'json');

		$this->assertJsonStringEqualsJsonString($json, '{"birthDate": 1234567 }');
	}

	/** @test */
	public function shouldDeserializeTimestampsAsDateTimes() {
		/** @var BirthdayBoy $birthdayBoy */
		$birthdayBoy = $this->serializer
			->deserialize('{"birthDate": 1234567 }', 'Aeris\ZendRestModuleTest\RestTestModule\Model\BirthdayBoy', 'json');

		$this->assertEquals(1234567, $birthdayBoy->getBirthDate()->getTimestamp());
	}

	/**
	 * @param int $timestamp
	 * @return \DateTime
	 */
	protected static function FromTimestamp($timestamp) {
		$date = new \DateTime();
		$date->setTimestamp($timestamp);

		return $date;
	}

}