<?php


namespace Aeris\ZfAuth\Identity;


/**
 * A user acting on behalf of an oAuth client
 *  eg, an oauth client requesting to login a user, or register a new user
 */
class OAuthClientIdentity implements IdentityInterface {

	public function getRoles() {
		return ['oauth_client'];
	}
}