<?php


namespace Aeris\ZfAuth\Factory;


use Aeris\ZfAuth\PluginManager\GuardManager;
use Aeris\ZfDiConfig\ServiceManager\ConfigPlugin\ConfigPluginManager;
use Aeris\ZfDiConfig\ServiceManager\DiConfig;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class GuardManagerFactory implements FactoryInterface {

	/**
	 * Create service
	 *
	 * @param ServiceLocatorInterface $serviceLocator
	 * @return GuardManager
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		$guardManager = new GuardManager();
		$guardManager->setServiceLocator($serviceLocator);

		// Accept ZfDiConfig configuration
		$diConfig = new DiConfig(@$serviceLocator->get('config')['zf_auth']['guard_manager']['di'] ?: []);

		$diConfig->setDefaultPlugin('$factory');

		/** @var ConfigPluginManager $pluginManager */
		$pluginManager = $serviceLocator->get('Aeris\ZfDiConfig\ServiceManager\ConfigPlugin\ConfigPluginManager');
		$diConfig->setPluginManager($pluginManager);

		$diConfig->configureServiceManager($guardManager);

		return $guardManager;
	}
}