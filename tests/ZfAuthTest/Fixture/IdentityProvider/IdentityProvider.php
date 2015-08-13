<?php


namespace Aeris\ZfAuthTest\Fixture\IdentityProvider;


use Aeris\ZfAuth\Identity\IdentityInterface;
use Aeris\ZfAuth\IdentityProvider\IdentityProviderInterface;
use Mockery as M;

class IdentityProvider implements IdentityProviderInterface {

	/** @var IdentityInterface */
	public $identity;

	public $_canAuthenticate = true;

	public function canAuthenticate() {
		return $this->_canAuthenticate;
	}

	/** @return IdentityInterface */
	public function getIdentity() {
		return $this->identity;
	}

	/**
	 * @param IdentityInterface $identity
	 */
	public function setIdentity(IdentityInterface $identity) {
		$this->identity = $identity;
	}

	public function setIdentityRoles(array $roles) {
		$this->identity = M::mock('\Aeris\ZfAuth\Identity\IdentityInterface', [
			'getRoles' => $roles,
		]);
	}

	/**
	 * @param boolean $canAuthenticate
	 */
	public function setCanAuthenticate($canAuthenticate) {
		$this->_canAuthenticate = $canAuthenticate;
	}


}