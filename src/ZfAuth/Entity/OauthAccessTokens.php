<?php

namespace Aeris\ZfAuth\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OauthAccessTokens
 *
 * @ORM\Table(name="oauth_access_tokens")
 * @ORM\Entity
 */
class OauthAccessTokens {
	/**
	 * @var string
	 *
	 * @ORM\Column(name="access_token", type="string", length=40, nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $accessToken;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="expires", type="datetime", nullable=false)
	 */
	private $expires;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="scope", type="string", length=2000, nullable=true)
	 */
	private $scope;


	/**
	 * Get accessToken
	 *
	 * @return string
	 */
	public function getAccessToken() {
		return $this->accessToken;
	}

	/**
	 * Set expires
	 *
	 * @param \DateTime $expires
	 * @return OauthAccessTokens
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
	 * Set scope
	 *
	 * @param string $scope
	 * @return OauthAccessTokens
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


}
