<?php


namespace Aeris\ZfAuthTest\Fixture\ServiceManager;


use Zend\ServiceManager\Exception;
use Zend\ServiceManager\ServiceLocatorInterface;

class ServiceLocator implements ServiceLocatorInterface {
	protected $services = [];

	public function get($name, array $params = []) {
		if (!isset($this->services[$name])) {
			return null;
		}

		return $this->services[$name];
	}

	public function has($name) {
		return (isset($this->services[$name]));
	}

	public function set($name, $object) {
		$this->services[$name] = $object;
	}
}
