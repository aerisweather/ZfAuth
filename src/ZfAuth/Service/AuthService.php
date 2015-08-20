<?php


namespace Aeris\ZfAuth\Service;


use Aeris\ZfAuth\Identity\IdentityInterface;
use Aeris\ZfAuth\IdentityProvider\IdentityProviderInterface;
use Aeris\ZfAuth\Token\IdentityToken;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;

class AuthService implements AuthServiceInterface {

	/** @var IdentityProviderInterface */
	protected $identityProvider;

	/** @var AccessDecisionManager */
	protected $accessDecisionManager;

	public function isGranted($permission, $resource = null) {
		$token = IdentityToken::FromIdentity($this->getIdentity());

		return $this->accessDecisionManager
			->decide($token, [$permission], $resource);
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

	/**
	 * @param AccessDecisionManager $accessDecisionManager
	 */
	public function setAccessDecisionManager(AccessDecisionManager $accessDecisionManager) {
		$this->accessDecisionManager = $accessDecisionManager;
	}
}