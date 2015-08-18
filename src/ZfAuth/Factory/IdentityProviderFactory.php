<?php


namespace Aeris\ZfAuth\Factory;


use Aeris\ZfAuth\IdentityProvider\ChainedIdentityProvider;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class IdentityProviderFactory implements FactoryInterface {

	/**
	 * Create service
	 *
	 * @param ServiceLocatorInterface $serviceLocator
	 * @return mixed
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		$topLevelIdentityProvider = new ChainedIdentityProvider();

		$identityProviderServices = [
			'Aeris\ZfAuth\IdentityProvider\OAuthUserIdentityProvider',
			'Aeris\ZfAuth\IdentityProvider\OAuthClientIdentityProvider',
			'Aeris\ZfAuth\IdentityProvider\AnonymousIdentityProvider',
		];

		$providers = array_map([$serviceLocator, 'get'], $identityProviderServices);

		$topLevelIdentityProvider->setProviders($providers);

		return $topLevelIdentityProvider;
	}
}