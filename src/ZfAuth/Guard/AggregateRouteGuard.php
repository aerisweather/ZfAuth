<?php


namespace Aeris\ZfAuth\Guard;


use Zend\Mvc\Router\RouteMatch;
use Zend\ServiceManager\ServiceLocatorInterface;
use Aeris\Fn;

class AggregateRouteGuard implements GuardInterface {

	/** @var ServiceLocatorInterface */
	protected $guardManager;

	/** @var GuardInterface[] */
	protected $guards = [];

	public function __construct(array $rules = []) {
		if (count($rules)) {
			throw new \InvalidArgumentException('Unable to set rules on a AggregateRouteGuard until a RouteGuardManager is set.');
		}
	}

	public function setRules(array $rules) {
		$this->guards = [];

		foreach ($rules as $guardName => $guardRules) {
			/** @var GuardInterface $guard */
			$guard = $this->guardManager->get($guardName);
			$guard->setRules($guardRules);

			$this->guards[] = $guard;
		}
	}

	/** @return boolean */
	public function isGranted(RouteMatch $routeMatch) {
		// No guards --> not granted by default
		if (!count($this->guards)) {
			return false;
		}

		return Fn\all($this->guards, function(GuardInterface $guard) use ($routeMatch) {
			return $guard->isGranted($routeMatch);
		});
	}

	/**
	 * @param ServiceLocatorInterface $guardManager
	 */
	public function setGuardManager(ServiceLocatorInterface $guardManager) {
		$this->guardManager = $guardManager;
	}
}