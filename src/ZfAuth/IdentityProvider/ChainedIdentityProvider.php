<?php


namespace Aeris\ZfAuth\IdentityProvider;

use Aeris\ZfAuth\Identity\IdentityInterface;

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


	/** @return IdentityInterface */
	public function getIdentity() {
		$identity = null;

		foreach ($this->providers as $provider) {
			$identity = $provider->getIdentity();
			if ($identity !== null) {
				break;
			}
		}

		return $identity;
	}

	/**
	 * @param IdentityProviderInterface[] $providers
	 */
	public function setProviders(array $providers) {
		$this->providers = $providers;
	}
}