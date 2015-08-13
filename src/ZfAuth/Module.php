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
		$eventManager->attach(MvcEvent::EVENT_ROUTE, function(MvcEvent $event) use ($eventManager) {
			/** @var IdentityProviderInterface $identityProvider */
			$identityProvider = $event->getApplication()
				->getServiceManager()
				->get('Aeris\ZfAuth\IdentityProvider');


			if (!$identityProvider->canAuthenticate() || $identityProvider->getIdentity() === null) {
				$event->setError(self::AUTHENTICATION_ERROR);
				$event->setParam('exception', new AuthenticationException());
				$eventManager->trigger(MvcEvent::EVENT_DISPATCH_ERROR, $event);
			}
		}, 500);
	}

}