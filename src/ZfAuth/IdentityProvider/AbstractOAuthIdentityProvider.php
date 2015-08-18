<?php


namespace Aeris\ZfAuth\IdentityProvider;

use OAuth2\Request as OAuth2Request;
use OAuth2\Server;

/**
 * Base class for identity providers using OAuth
 */
abstract class AbstractOAuthIdentityProvider implements IdentityProviderInterface {
	/** @var OAuth2Request */
	protected $request;

	/** @var Server */
	protected $oauthServer;

	protected function param($name) {
		return $this->request->request($name, $this->request->query($name));
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