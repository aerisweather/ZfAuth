<?php


namespace Aeris\ZfAuth\Identity;


/**
 * A user acting on behalf of an oAuth client
 *  eg, an oauth client requesting to login a user, or register a new user
 */
class OAuthClientIdentity implements IdentityInterface {

	/** @var string */
	protected $clientId;

	public function getRoles() {
		return ['oauth_client'];
	}

	public function getClientId() {
		return $this->clientId;
	}

	/**
	 * @param string $clientId
	 */
	public function setClientId($clientId) {
		$this->clientId = $clientId;
	}
}