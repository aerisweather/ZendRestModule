<?php


namespace Aeris\ZendRestModule\View\Annotation;

/**
 * @Annotation
 * @Target({"METHOD"})
 *
 * Specify a serialization group for a controller action.
 */
class Groups {

	/** @var array<string> */
	protected $groups;

	public function __construct(array $groups) {
		$this->groups = $groups['value'];
	}

	/**
	 * @return array
	 */
	public function getGroups() {
		return $this->groups;
	}

}