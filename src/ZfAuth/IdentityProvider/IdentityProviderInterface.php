<?php

namespace Aeris\ZfAuth\IdentityProvider;


use Aeris\ZfAuth\Identity\IdentityInterface;

interface IdentityProviderInterface {

	/**
	 * Can this provider authenticate the current request?
	 *
	 * Should return true if the means for authentication are available,
	 * even if the user cannot be authenticate
	 * (eg. a username/password are provided, but they do not match a real user)
	 *
	 * This lets us know whether authentication failed because we didn't have enough info,
	 * or because the info was incorrect
	 *
	 * @return boolean
	 */
	public function canAuthenticate();

	/** @return IdentityInterface */
	public function getIdentity();

}