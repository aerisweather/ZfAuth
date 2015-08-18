<?php


namespace Aeris\ZfAuth\Service;


use Aeris\ZfAuth\Identity\IdentityInterface;
use Aeris\ZfAuth\IdentityProvider\IdentityProviderInterface;

class AuthService implements AuthServiceInterface {

	/** @var IdentityProviderInterface */
	protected $identityProvider;

	public function isGranted($permission, $resource = null) {
		throw new \Exception('Not yet implemented. We will use voters for this');
	}

	/** @return IdentityInterface */
	public function getIdentity() {
		return $this->identityProvider->getIdentity();
	}

	/**
	 * @param IdentityProviderInterface $identityProvider
	 */
	public function setIdentityProvider(IdentityProviderInterface $identityProvider) {
		$this->identityProvider = $identityProvider;
	}
}