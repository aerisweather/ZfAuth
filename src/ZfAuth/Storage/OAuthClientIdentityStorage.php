<?php


namespace Aeris\ZfAuth\Storage;


use Aeris\ZfAuth\Identity\OAuthClientIdentity;
use OAuth2\Server;
use OAuth2\Request as OAuth2Request;
use Zend\Authentication\Storage\NonPersistent;

class OAuthClientIdentityStorage extends NonPersistent {

	/** @var OAuth2Request */
	protected $request;

	/** @var Server */
	protected $oauthServer;

	public function read() {
		if ($this->isEmpty()) {
			$this->write($this->getIdentity());
		}
		return parent::read();
	}

	private function getIdentity() {
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

		return new OAuthClientIdentity();
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