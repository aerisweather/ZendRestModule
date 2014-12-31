<?php


namespace Aeris\ZendRestModule\Service\Serializer\Naming;


use JMS\Serializer\Naming\PropertyNamingStrategyInterface;
use JMS\Serializer\Metadata\PropertyMetadata;

class IdenticalPropertyNamingStrategy implements PropertyNamingStrategyInterface {

	public function translateName(PropertyMetadata $property) {
		if ($property->serializedName) {
			return $property->serializedName;
		}
		return $property->name;
	}

}
