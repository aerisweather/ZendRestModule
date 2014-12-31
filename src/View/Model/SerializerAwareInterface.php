<?php
/**
 * Created by PhpStorm.
 * User: edanschwartz
 * Date: 7/30/14
 * Time: 9:22 AM
 */

namespace Aeris\ZendRestModule\View\Model;


use Aeris\ZendRestModule\Service\Serializer\SerializerInterface;

interface SerializerAwareInterface {

	public function setSerializer(SerializerInterface $serializer);

	/**
	 * @return \Aeris\ZendRestModule\Service\Serializer\SerializerInterface
	 */
	public function getSerializer();

}
