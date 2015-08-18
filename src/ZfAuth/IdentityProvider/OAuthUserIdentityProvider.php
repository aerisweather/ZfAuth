<?php


namespace Aeris\ZfAuth\IdentityProvider;


use Aeris\ZfAuth\Identity\IdentityInterface;
use Aeris\ZfAuth\Repository\IdentityRepositoryInterface;

class OAuthUserIdentityProvider extends AbstractOAuthIdentityProvider {

	/** @var IdentityRepositoryInterface */
	protected $identityAdapter;

	public function canAuthenticate() {
		return $this->param('access_token') !== null;
	}

	/** @return IdentityInterface */
	public function getIdentity() {
		$accessToken = $this->param('access_token');

		if ($accessToken === null) {
			return null;
		}

		$accessTokenData = $this->oauthServer->getAccessTokenData($this->request);

		$identity = $this->identityAdapter
			->findByUsername($accessTokenData['user_id']);

		if ($identity === null) {
			return null;
		}
		else if (!($identity instanceof IdentityInterface)) {
			throw new \UnexpectedValueException('Expected IdentityAdapter to return an IdentityInterface.');
		}

		return $this->identity = $identity;
	}

	/** @param IdentityRepositoryInterface $identityAdapter */
	public function setIdentityAdapter(IdentityRepositoryInterface $identityAdapter) {
		$this->identityAdapter = $identityAdapter;
	}

	/**
	 * @param string $oauthClientIdentityClass
	 */
	public function setOauthClientIdentityClass($oauthClientIdentityClass) {
		$this->oauthClientIdentityClass = $oauthClientIdentityClass;
	}
}