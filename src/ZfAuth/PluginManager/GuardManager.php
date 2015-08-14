<?php


namespace Aeris\ZfAuth\PluginManager;


use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception;

class GuardManager extends AbstractPluginManager {

	/**
	 * Validate the plugin
	 *
	 * Checks that the filter loaded is either a valid callback or an instance
	 * of FilterInterface.
	 *
	 * @param  mixed $plugin
	 * @return void
	 * @throws Exception\RuntimeException if invalid
	 */
	public function validatePlugin($plugin) {
		if (!($plugin instanceof \Aeris\ZfAuth\Guard\GuardInterface)) {
			throw new Exception\RuntimeException('Invalid guard plugin.');
		};
	}
}