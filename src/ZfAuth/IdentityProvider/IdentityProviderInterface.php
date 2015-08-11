<?php

namespace Aeris\ZfAuth\IdentityProvider;


use Aeris\ZfAuth\Identity\IdentityInterface;

interface IdentityProviderInterface {

	/** @return IdentityInterface */
	public function getIdentity();

}