<?php


namespace Aeris\ZfAuth\Storage;


use Aeris\ZfAuth\StorageAdapter\IdentityStorageAdapterInterface;
use OAuth2\Request as OAuth2Request;
use OAuth2\Server;
use Zend\Authentication\Storage\NonPersistent;

class OAuthUserStorage extends NonPersistent {

	/** @var Server */
	protected $oauthServer;

	/** @var OAuth2Request */
	protected $request;
	// we can get this from $sm->get('Application')->getMvcEvent()->getRequest();

	/** @var IdentityStorageAdapterInterface */
	protected $identityStorageAdapter;

	/** @var mixed */
	protected $identity;

	public function read() {
		if ($this->isEmpty()) {
			$this->write($this->getIdentity());
		}
		return parent::read();
	}

	/** @return null|mixed */
	protected function getIdentity() {
		$accessToken = $this->request->query('access_token');

		if ($accessToken === null) {
			return null;
		}

		$accessTokenData = $this->oauthServer->getAccessTokenData($this->request);

		return $this->identity = $this->identityStorageAdapter
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

	/** @param IdentityStorageAdapterInterface $identityStorageAdapter */
	public function setIdentityStorageAdapter(IdentityStorageAdapterInterface $identityStorageAdapter) {
		$this->identityStorageAdapter = $identityStorageAdapter;
	}

}