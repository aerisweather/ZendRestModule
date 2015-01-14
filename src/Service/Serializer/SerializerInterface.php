<?php


namespace Aeris\ZendRestModule\Service\Serializer;

use JMS\Serializer\SerializationContext;


interface SerializerInterface
{
	public function serialize($data, $format, SerializationContext $context = null);

	/**
	 * Deserialize
	 *
	 * JMS Deserialized has been wrapped to allow looser typing of arguments.
	 * @param string|array $data
	 * @param string|\StdClass $object
	 * @param string $format
	 * @return mixed
	 */
	public function deserialize($data, $object, $format = 'json');
}
