<?php

namespace Aeris\ZfAuth\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OauthRefreshTokens
 *
 * @ORM\Table(name="oauth_refresh_tokens")
 * @ORM\Entity
 */
class OAuthRefreshToken {
	/**
	 * @var string
	 *
	 * @ORM\Column(name="refresh_token", type="string", length=40, nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $refreshToken;

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
	 * Get refreshToken
	 *
	 * @return string
	 */
	public function getRefreshToken() {
		return $this->refreshToken;
	}

	/**
	 * Set expires
	 *
	 * @param \DateTime $expires
	 * @return OAuthRefreshToken
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
	 * @return OAuthRefreshToken
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
