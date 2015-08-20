<?php


namespace Aeris\ZfAuth\Token;


use Aeris\ZfAuth\Identity\IdentityInterface;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Aeris\Fn;

/**
 * A bridge between our IdentityInterface objects
 * and Symfony TokenInterface objects
 */
class IdentityToken extends AbstractToken {

	/** @var IdentityInterface */
	protected $identity;

	public static function FromIdentity(IdentityInterface $identity) {
		$token = new self();
		$token->setUser($identity);

		return $token;
	}

	/** @param IdentityInterface $user */
	public function setUser($user) {
		if (!($user instanceof IdentityInterface)) {
			throw new \InvalidArgumentException('IdentityToken::setUser requires an IdentityInterface object');
		}

		$this->identity = $user;
	}

	/** @return IdentityInterface */
	public function getUser() {
		return $this->identity;
	}

	public function getRoles() {
		return array_map($this->identity->getRoles(), Fn\factory('\Symfony\Component\Security\Core\Role\Role'));
	}

	/**
	 * Returns the user credentials.
	 *
	 * @return mixed The user credentials
	 */
	public function getCredentials() {
		return '';
	}

	public function getUsername() {
		return method_exists($this->identity, 'getUsername') ?
			$this->identity->getUsername() : null;
	}
}