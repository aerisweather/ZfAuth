<?php


namespace Aeris\ZfAuth\IdentityProvider;


use Aeris\ZfAuth\Identity\AnonymousIdentity;
use Aeris\ZfAuth\Identity\IdentityInterface;

class AnonymousIdentityProvider implements IdentityProviderInterface {

	/** @return IdentityInterface */
	public function getIdentity() {
		return new AnonymousIdentity();
	}
}