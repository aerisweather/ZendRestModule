<?php

namespace Aeris\ZendRestModule\Service\Serializer\Handler;

use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\VisitorInterface;

class DateTimeTimestampHandler implements SubscribingHandlerInterface
{
	public static function getSubscribingMethods() {
		return array(
			array(
				'type'      => 'DateTimeTimestamp',
				'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
				'format'    => 'json',
			),
			$methods[] = array(
				'type'      => 'DateTimeTimestamp',
				'format'    => 'json',
				'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
				'method'    => 'serializeDateTimeStamp',
			)
		);
	}

	public function serializeDateTimeStamp(VisitorInterface $visitor, \DateTime $date, array $type, Context $context)
	{
		return (int)$visitor->visitString($date->format('U'), $type, $context);
	}

	public function deserializeDateTimeTimestampFromjson(JsonDeserializationVisitor $visitor, $data, array $type)
	{
		if (null === $data) {
			return null;
		}

		return new \DateTime('@'.$data);
	}



}
