<?php


namespace Aeris\ZfAuth\Storage;


use Aeris\ZfAuth\Request\OAuth2RequestFactory;
use Aeris\ZfAuth\StorageAdapter\IdentityStorageAdapterInterface;
use OAuth2\Server;
use Zend\Authentication\Storage\NonPersistent;
use Zend\Http\Request as HttpRequest;

class OAuthUserStorage extends NonPersistent {

	/** @var Server */
	protected $oauthServer;

	/** @var HttpRequest */
	protected $request;
	// we can get this from $sm->get('Application')->getMvcEvent()->getRequest();

	/** @var IdentityStorageAdapterInterface */
	protected $identityStorageAdapter;

	/** @var mixed */
	protected $identity;

	public function isEmpty() {
		$hasOAuthRequestParams = $this->request->getQuery('access_token');

		if (!$hasOAuthRequestParams) {
			return false;
		}

		return !$this->getIdentity();
	}

	public function read() {
		return $this->getIdentity();
	}

	public function clear() {
		$this->identity = null;
	}

	/** @return null|mixed */
	protected function getIdentity() {
		if ($this->identity) {
			return $this->identity;
		}

		$accessToken = $this->request->getQuery('access_token', $this->request->getPost('access_token'));

		if ($accessToken === null) {
			return null;
		}

		$oAuthRequest = OAuth2RequestFactory::create($this->request);
		$accessTokenData = $this->oauthServer->getAccessTokenData($oAuthRequest);

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
	 * @param HttpRequest $request
	 * @return $this
	 */
	public function setRequest(HttpRequest $request) {
		$this->request = $request;
	}

	/** @param IdentityStorageAdapterInterface $identityStorageAdapter */
	public function setIdentityStorageAdapter(IdentityStorageAdapterInterface $identityStorageAdapter) {
		$this->identityStorageAdapter = $identityStorageAdapter;
	}

}