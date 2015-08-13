<?php


namespace Aeris\ZfAuth\IdentityProvider;

use Aeris\ZfAuth\Identity\OAuthClientIdentity;
use OAuth2\Server;
use OAuth2\Request as OAuth2Request;
use Aeris\Fn;

/**
 * Provides a user acting on behalf of an oAuth client.
 * Does not require an access_token (only client_id/client_secret)
 */
class OAuthClientIdentityProvider implements IdentityProviderInterface {
	/** @var OAuth2Request */
	protected $request;

	/** @var Server */
	protected $oauthServer;

	/**
	 * Can this provider authenticate the current request?
	 *
	 * Should return true if the means for authentication are available,
	 * even if the user cannot be authenticate
	 * (eg. a username/password are provided, but they do not match a real user)
	 *
	 * @return boolean
	 */
	public function canAuthenticate() {
		return Fn\all(['client_id', 'client_secret'], function($k) {
			return $this->request->query($k) !== null;
		});
	}

	public function getIdentity() {
		// A little hack so client's don't have to add these
		// (because we aren't actually sending back a client oauth code,
		//   we're just validating that the client is authorized).
		$this->request->query['response_type'] = 'code';
		$this->request->query['state'] = 'true';


		$isWellFormedRequest = $this->oauthServer->validateAuthorizeRequest($this->request);
		if (!$isWellFormedRequest) {
			return null;
		}

		/** @var \OAuth2\Storage\Pdo $clientStorage */
		$clientStorage = $this->oauthServer->getStorage('client');
		$isAuthorizedRequest = $clientStorage->checkClientCredentials(
			$this->request->query('client_id'),
			$this->request->query('client_secret')
		);

		if (!$isAuthorizedRequest) {
			return null;
		}

		$clientIdentity = new OAuthClientIdentity();
		$clientIdentity->setClientId($this->request->query('client_id'));
		return $clientIdentity;
	}

	/**
	 * @param OAuth2Request $request
	 */
	public function setRequest(OAuth2Request $request) {
		$this->request = $request;
	}

	/**
	 * @param Server $oauthServer
	 */
	public function setOauthServer(Server $oauthServer) {
		$this->oauthServer = $oauthServer;
	}
}