<?php

namespace Aeris\ZfAuth;


use Aeris\ZfAuth\Exception\AuthenticationException;
use Aeris\ZfAuth\Exception\GuardAuthorizationException;
use Aeris\ZfAuth\Guard\GuardInterface;
use Aeris\ZfAuth\IdentityProvider\IdentityProviderInterface;
use Aeris\ZfAuthTest\Functional\Authorization\RouteGuardsTest;
use Zend\Mvc\MvcEvent;
use Zend\Navigation\Page\Mvc;

class Module {

	const AUTHENTICATION_ERROR = 'zf-auth-authentication-error';
	const GUARD_AUTHORIZATION_ERROR = 'zf-auth-guard-authorization-error';

	public function getConfig() {
		return include __DIR__ . '/config/module.config.php';
	}

	public function onBootstrap(MvcEvent $event) {
		$eventManager = $event->getApplication()
			->getEventManager();

		$eventManager->attach(MvcEvent::EVENT_ROUTE, [$this, 'checkAuthenticated'], 500);
		$eventManager->attach(MvcEvent::EVENT_ROUTE, [$this, 'guardRoutes'], -5);
	}

	public function checkAuthenticated(MvcEvent $event) {
		/** @var IdentityProviderInterface $identityProvider */
		$identityProvider = $event->getApplication()
			->getServiceManager()
			->get('Aeris\ZfAuth\IdentityProvider');


		if (!$identityProvider->canAuthenticate() || $identityProvider->getIdentity() === null) {
			$this->terminateEvent($event, self::AUTHENTICATION_ERROR, new AuthenticationException());
		}
	}

	public function guardRoutes(MvcEvent $event) {
		/** @var GuardInterface $routeGuard */
		$routeGuard = $event->getApplication()
			->getServiceManager()
			->get('Aeris\ZfAuth\RouteGuard');

		if ($routeGuard->isGranted($event->getRouteMatch())) {
			return;
		}

		$this->terminateEvent($event, self::GUARD_AUTHORIZATION_ERROR, new GuardAuthorizationException());
	}

	protected function terminateEvent(MvcEvent $event, $error, \Exception $exception) {
		$eventManager = $event->getApplication()
			->getEventManager();

		$event->setError($error);
		$event->setParam('exception', $exception);

		$event->stopPropagation(true);

		$eventManager->trigger(MvcEvent::EVENT_DISPATCH_ERROR, $event);
	}

}