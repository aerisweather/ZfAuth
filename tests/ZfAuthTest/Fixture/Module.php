<?php


namespace Aeris\ZfAuthTest\Fixture;


class Module {

	public function getConfig() {
		// Use a static config,
		// so we can easily overwrite it in tests.
		return ModuleConfig::getConfig();
	}

}