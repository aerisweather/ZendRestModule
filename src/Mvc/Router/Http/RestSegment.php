<?php


namespace Aeris\ZendRestModule\Mvc\Router\Http;

use Zend\Mvc\Router\Http\Segment as ZendSegment;
use Zend\Stdlib\RequestInterface as Request;

class RestSegment extends ZendSegment
{
	protected $identifierName = 'id';

	/**
	 * Set the route match/query parameter name containing the identifier
	 *
	 * @param  string $name
	 * @return self
	 */
	public function setIdentifierName($name) {
		$this->identifierName = (string)$name;
		return $this;
	}

	/**
	 * Retrieve the route match/query parameter name containing the identifier
	 *
	 * @return string
	 */
	public function getIdentifierName() {
		return $this->identifierName;
	}

	/**
	 * Match (for REST)
	 *
	 * Add a 'restAction' param to the RouteMatch object to indicate the action that will be taken by the
	 * AbstractRestfulController
	 *
	 * @param Request $request
	 * @param null $pathOffset
	 * @param array $options
	 * @return null|\Zend\Mvc\Router\Http\RouteMatch
	 */
	public function match(Request $request, $pathOffset = null, array $options = array()) {
		$routeMatch = parent::match($request, $pathOffset, $options);

		if ($routeMatch) {
			// If the route matched for this router then...
			$action = $routeMatch->getParam('action', false);
			if (!$action) {
				// Ignore actions that were already set, those get mapped to custom http methods.
				// RESTful methods
				$method = strtolower($request->getMethod());
				switch ($method) {
					// DELETE
					case 'delete':
						$id = $this->getIdentifier($routeMatch, $request);
						if ($id !== false) {
							$action = 'delete';
							break;
						}
						$action = 'deleteList';
						break;
					// GET
					case 'get':
						$id = $this->getIdentifier($routeMatch, $request);
						if ($id !== false) {
							$action = 'get';
							break;
						}
						$action = 'getList';
						break;
					// HEAD
					case 'head':
						$action = 'head';
						break;
					// OPTIONS
					case 'options':
						$action = 'options';
						break;
					// PATCH
					case 'patch':
						$id = $this->getIdentifier($routeMatch, $request);

						if ($id !== false) {
							$action = 'patch';
							break;
						}
						$action = 'patchList';
						break;
					// POST
					case 'post':
						$action = 'create';
						break;
					// PUT
					case 'put':
						$id = $this->getIdentifier($routeMatch, $request);

						if ($id !== false) {
							$action = 'update';
							break;
						}
						$action = 'replaceList';
						break;
					// All others...
					default:
						$action = null;
				}
			}
			$routeMatch->setParam('restAction', $action);
		}
		return $routeMatch;
	}

	protected function getIdentifier($routeMatch, $request) {
		$identifier = $this->getIdentifierName();
		$id         = $routeMatch->getParam($identifier, false);
		if ($id !== false) {
			return $id;
		}

		$id = $request->getQuery()->get($identifier, false);
		if ($id !== false) {
			return $id;
		}
		return false;
	}
}
