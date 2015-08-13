<?php


namespace Aeris\ZfAuth\IdentityProvider;


use Aeris\ZfAuth\Identity\IdentityInterface;
use Aeris\ZfAuth\Repository\IdentityRepositoryInterface;
use OAuth2\Request as OAuth2Request;
use OAuth2\Server;

class OAuthUserIdentityProvider implements IdentityProviderInterface {

	/** @var Server */
	protected $oauthServer;

	/** @var OAuth2Request */
	protected $request;

	/** @var IdentityRepositoryInterface */
	protected $identityAdapter;

	public function canAuthenticate() {
		return $this->request->query('access_token') !== null;
	}

	/** @return IdentityInterface */
	public function getIdentity() {
		$accessToken = $this->request->query('access_token');

		if ($accessToken === null) {
			return null;
		}

		$accessTokenData = $this->oauthServer->getAccessTokenData($this->request);

		return $this->identity = $this->identityAdapter
			->findByUsername($accessTokenData['user_id']);
	}

	/**
	 * @param Server $oauthServer
	 * @return $this
	 */
	public function setOAuthServer(Server $oauthServer) {
		$this->oauthServer = $oauthServer;
	}

	/**
	 * @param OAuth2Request $request
	 * @return $this
	 */
	public function setRequest(OAuth2Request $request) {
		$this->request = $request;
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