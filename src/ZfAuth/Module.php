<?php

namespace Aeris\ZfAuth;


use Aeris\ZfAuth\Exception\AuthenticationException;
use Aeris\ZfAuth\IdentityProvider\IdentityProviderInterface;
use Zend\Mvc\MvcEvent;

class Module {

	const AUTHENTICATION_ERROR = 'zf-auth-authentication-error';

	public function getConfig() {
		return include __DIR__ . '/config/module.config.php';
	}

	public function onBootstrap(MvcEvent $event) {
		$eventManager = $event->getApplication()
			->getEventManager();
		$eventManager->attach(MvcEvent::EVENT_ROUTE, [$this, 'checkAuthenticated'], 500);
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

	protected function terminateEvent(MvcEvent $event, $error, \Exception $exception) {
		$eventManager = $event->getApplication()
			->getEventManager();

		$event->setError($error);
		$event->setParam('exception', $exception);

		$event->stopPropagation(true);

		$eventManager->trigger(MvcEvent::EVENT_DISPATCH_ERROR, $event);
	}


}