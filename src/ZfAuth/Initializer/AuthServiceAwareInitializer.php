<?php


namespace Aeris\ZfAuth\Initializer;


use Aeris\ZfAuth\Service\AuthServiceAwareInterface;
use Aeris\ZfAuth\Service\AuthServiceInterface;
use Zend\ServiceManager\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AuthServiceAwareInitializer implements InitializerInterface {

	/**
	 * Initialize
	 *
	 * @param                         $instance
	 * @param ServiceLocatorInterface $serviceLocator
	 * @return mixed
	 */
	public function initialize($instance, ServiceLocatorInterface $serviceLocator) {
		if ($serviceLocator instanceof ServiceLocatorAwareInterface) {
			$serviceLocator = $serviceLocator->getServiceLocator();
		}

		if ($instance instanceof AuthServiceAwareInterface) {
			/** @var AuthServiceInterface $authService */
			$authService = $serviceLocator->get('Aeris\ZfAuth\Service\AuthService');
			$instance->setAuthService($authService);
		}
	}
}