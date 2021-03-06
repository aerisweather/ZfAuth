<?php

namespace Aeris\ZfAuth\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OauthAuthorizationCodes
 *
 * @ORM\Table(name="oauth_authorization_codes")
 * @ORM\Entity
 */
class OAuthAuthorizationCode {
	/**
	 * @var string
	 *
	 * @ORM\Column(name="authorization_code", type="string", length=40, nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $authorizationCode;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="redirect_uri", type="string", length=1024, nullable=true)
	 */
	private $redirectUri;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="expires", type="datetime", nullable=false)
	 */
	private $expires;

	/**
	 * @var \Aeris\ZfAuth\Entity\OAuthScope
	 *
	 * @ORM\ManyToOne(targetEntity="Aeris\ZfAuth\Model\OauthScopes")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="oauth_scopes_scope", referencedColumnName="scope")
	 * })
	 */
	private $oauthScopesScope;

	/**
	 * @var \Aeris\ZfAuth\Entity\OAuthClient
	 *
	 * @ORM\ManyToOne(targetEntity="Aeris\ZfAuth\Model\OauthClients")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="oauth_clients_client_id", referencedColumnName="client_id")
	 * })
	 */
	private $oauthClientsClient;


	/**
	 * Get authorizationCode
	 *
	 * @return string
	 */
	public function getAuthorizationCode() {
		return $this->authorizationCode;
	}

	/**
	 * Set redirectUri
	 *
	 * @param string $redirectUri
	 * @return OAuthAuthorizationCode
	 */
	public function setRedirectUri($redirectUri) {
		$this->redirectUri = $redirectUri;

		return $this;
	}

	/**
	 * Get redirectUri
	 *
	 * @return string
	 */
	public function getRedirectUri() {
		return $this->redirectUri;
	}

	/**
	 * Set expires
	 *
	 * @param \DateTime $expires
	 * @return OAuthAuthorizationCode
	 */
	public function setExpires($expires) {
		$this->expires = $expires;

		return $this;
	}

	/**
	 * Get expires
	 *
	 * @return \DateTime
	 */
	public function getExpires() {
		return $this->expires;
	}

	/**
	 * Set oauthScopesScope
	 *
	 * @param \Aeris\ZfAuth\Entity\OAuthScope $oauthScopesScope
	 * @return OAuthAuthorizationCode
	 */
	public function setOauthScopesScope(\Aeris\ZfAuth\Entity\OAuthScope $oauthScopesScope = null) {
		$this->oauthScopesScope = $oauthScopesScope;

		return $this;
	}

	/**
	 * Get oauthScopesScope
	 *
	 * @return \Aeris\ZfAuth\Entity\OAuthScope
	 */
	public function getOauthScopesScope() {
		return $this->oauthScopesScope;
	}

	/**
	 * Set oauthClientsClient
	 *
	 * @param \Aeris\ZfAuth\Entity\OAuthClient $oauthClientsClient
	 * @return OAuthAuthorizationCode
	 */
	public function setOauthClientsClient(\Aeris\ZfAuth\Entity\OAuthClient $oauthClientsClient = null) {
		$this->oauthClientsClient = $oauthClientsClient;

		return $this;
	}

	/**
	 * Get oauthClientsClient
	 *
	 * @return \Aeris\ZfAuth\Entity\OAuthClient
	 */
	public function getOauthClientsClient() {
		return $this->oauthClientsClient;
	}


}
