<?php

namespace Aeris\ZfAuth\Entity;

use Aeris\ZfAuth\Service\Encryptor;
use Aeris\ZfAuth\Service\EncryptorInterface;
use Doctrine\ORM\Mapping as ORM;



/**
 * OauthClients
 *
 * @ORM\Table(name="oauth_clients")
 * @ORM\Entity
 */
class OAuthClient {
	/**
	 * @var string
	 *
	 * @ORM\Column(name="client_id", type="string", length=80, nullable=false)
	 * @ORM\Id
	 */
	private $clientId;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="client_secret", type="string", length=80, nullable=false)
	 */
	private $clientSecret;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="redirect_uri", type="string", length=2000, nullable=false)
	 */
	private $redirectUri;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="grant_types", type="string", length=80, nullable=true)
	 */
	private $grantTypes;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="scope", type="string", length=100, nullable=true)
	 */
	private $scope;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="user_id", type="string", length=80, nullable=true)
	 */
	private $userId;

	/**  @var EncryptorInterface */
	private $encryptor;



	/**
	 * @return Encryptor
	 */
	private function getEncryptor() {
		if (!is_null($this->encryptor)) {
			return $this->encryptor;
		}

		return $this->encryptor = new Encryptor();
	}


	/**
	 * Get clientId
	 *
	 * @return string
	 */
	public function getClientId() {
		return $this->clientId;
	}

	/**
	 * Encrypts and sets clientSecret
	 *
	 * @param string $rawClientSecret
	 * @return OAuthClient
	 */
	public function setClientSecret($rawClientSecret) {
		$this->clientSecret = $this->getEncryptor()
			->encrypt($rawClientSecret);

		return $this;
	}

	public function setEncryptedClientSecret($clientSecret) {
		$this->clientSecret = $clientSecret;
	}

	/**
	 * Get clientSecret
	 *
	 * @return string
	 */
	public function getClientSecret() {
		return $this->clientSecret;
	}

	/**
	 * Set redirectUri
	 *
	 * @param string $redirectUri
	 * @return OAuthClient
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
	 * Set grantTypes
	 *
	 * @param string $grantTypes
	 * @return OAuthClient
	 */
	public function setGrantTypes($grantTypes) {
		$this->grantTypes = $grantTypes;

		return $this;
	}

	/**
	 * Get grantTypes
	 *
	 * @return string
	 */
	public function getGrantTypes() {
		return $this->grantTypes;
	}

	/**
	 * Set scope
	 *
	 * @param string $scope
	 * @return OAuthClient
	 */
	public function setScope($scope) {
		$this->scope = $scope;

		return $this;
	}

	/**
	 * Get scope
	 *
	 * @return string
	 */
	public function getScope() {
		return $this->scope;
	}

	/**
	 * Set userId
	 *
	 * @param string $userId
	 * @return OAuthClient
	 */
	public function setUserId($userId) {
		$this->userId = $userId;

		return $this;
	}

	/**
	 * Get userId
	 *
	 * @return string
	 */
	public function getUserId() {
		return $this->userId;
	}

	/**
	 * @param string $clientId
	 */
	public function setClientId($clientId) {
		$this->clientId = $clientId;
	}
}
