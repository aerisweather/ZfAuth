<?php


namespace Aeris\ZfAuth\IdentityProvider;

use Aeris\ZfAuth\Identity\IdentityInterface;
use Aeris\Fn;

/**
 * Checks a set of IdentityProviders, and provides the identity from
 * the first provider to respond.
 */
class ChainedIdentityProvider implements IdentityProviderInterface {

	/** @var IdentityProviderInterface[] */
	protected $providers;

	/**
	 * @param IdentityProviderInterface[] $providers
	 */
	public function __construct(array $providers = []) {
		$this->providers = $providers;
	}


	public function canAuthenticate() {
		return $this->findProvider() !== null;
	}


	/** @return IdentityInterface */
	public function getIdentity() {
		$provider = $this->findProvider();

		if (!$provider) {
			return null;
		}

		return $provider->getIdentity();
	}

	/** @return IdentityProviderInterface */
	protected function findProvider() {
		return Fn\find($this->providers, function(IdentityProviderInterface $provider) {
			return $provider->canAuthenticate();
		});
	}

	/**
	 * @param IdentityProviderInterface[] $providers
	 */
	public function setProviders(array $providers) {
		$this->providers = $providers;
	}
}