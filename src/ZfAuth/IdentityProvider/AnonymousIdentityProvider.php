<?php


namespace Aeris\ZfAuth\IdentityProvider;


use Aeris\ZfAuth\Identity\AnonymousIdentity;
use Aeris\ZfAuth\Identity\IdentityInterface;

class AnonymousIdentityProvider implements IdentityProviderInterface {

	/**
	 * Can this provider authenticate the current request?
	 *
	 * Should return true if the means for authentication are available,
	 * even if the user cannot be authenticate
	 * (eg. a username/password are provided, but they do not match a real user)
	 *
	 * @return boolean
	 */
	public function canAuthenticate() {
		return true;
	}

	/** @return IdentityInterface */
	public function getIdentity() {
		return new AnonymousIdentity();
	}
}