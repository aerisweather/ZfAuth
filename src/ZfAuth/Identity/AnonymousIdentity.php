<?php


namespace Aeris\ZfAuth\Identity;


class AnonymousIdentity implements IdentityInterface {

	public function getRoles() {
		return ['anonymous'];
	}

}