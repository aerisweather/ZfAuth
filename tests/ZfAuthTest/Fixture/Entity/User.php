<?php

namespace Aeris\ZfAuthTest\Fixture\Entity;


use Aeris\ZfAuth\Identity\IdentityInterface;

class User implements IdentityInterface {

	public function getRoles() {
		return ['user'];
	}
}