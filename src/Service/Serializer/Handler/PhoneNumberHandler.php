<?php

namespace Aeris\ZendRestModule\Service\Serializer\Handler;

use Aeris\ZendRestModule\Service\Serializer\Handler\PhoneNumber\PhoneNumberFormatter;
use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\VisitorInterface;

class PhoneNumberHandler implements SubscribingHandlerInterface
{
	public static function getSubscribingMethods() {
		return array(
			array(
				'type'      => 'PhoneNumber',
				'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
				'format'    => 'json',
			),
			$methods[] = array(
				'type'      => 'PhoneNumber',
				'format'    => 'json',
				'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
				'method'    => 'serializePhoneNumber',
			)
		);
	}

	public function serializePhoneNumber(VisitorInterface $visitor, $phoneNumber, array $type, Context $context)
	{
		if (is_null($phoneNumber) || $phoneNumber == '') {
			return null;
		}

		$formatter = new PhoneNumberFormatter();
		$formattedPhoneNumber = $formatter->format($phoneNumber);

		return $visitor->visitString($formattedPhoneNumber, $type, $context);
	}

	/**
	 * @param JsonDeserializationVisitor $visitor
	 * @param $data
	 * @param array $type
	 * @return string|null
	 */
	public function deserializePhoneNumberFromJson(JsonDeserializationVisitor $visitor, $data, array $type)
	{
		if (null === $data) {
			return null;
		}

		return self::parsePhoneNumber($data);
	}

	/**
	 * Format a poorly formatted phone number into a nice string.
	 *
	 * @param $number
	 * @return mixed|null|string
	 */
	public static function parsePhoneNumber($number) {
		$number = preg_replace('/[^0-9x]/', '', $number);
		$number = self::addPhoneNumberCountryCode($number);
		if($number === '') {
			$number = null;
		}
		return $number;
	}

	/**
	 * Automatically add the US (+1) country code to 10 digit phone numbers.
	 *
	 * @param $number
	 * @return string
	 */
	public static function addPhoneNumberCountryCode($number) {
		//Assume 10 digit phone numbers are US (+1) phone numbers
		$numberParts = explode('x', $number);
		if(strlen($numberParts[0]) === 10) {
			return '1'.$number;
		}
		return $number;
	}

}
