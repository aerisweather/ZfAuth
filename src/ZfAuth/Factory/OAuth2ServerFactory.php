<?php


namespace Aeris\ZfAuth\Factory;


use OAuth2\Server;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZF\OAuth2\Factory\OAuth2ServerInstanceFactory;

class OAuth2ServerFactory implements FactoryInterface {

	/**
	 * Create service
	 *
	 * @param ServiceLocatorInterface $serviceLocator
	 * @return Server
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		/** @var OAuth2ServerInstanceFactory $instanceFactory */
		$instanceFactory = (new \ZF\OAuth2\Factory\OAuth2ServerFactory())->createService($serviceLocator);
		return $instanceFactory();
	}
}