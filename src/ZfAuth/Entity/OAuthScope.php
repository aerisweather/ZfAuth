<?php

namespace Aeris\ZfAuth\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OauthScopes
 *
 * @ORM\Table(name="oauth_scopes")
 * @ORM\Entity
 */
class OAuthScope {
	/**
	 * @var string
	 *
	 * @ORM\Column(name="scope", type="string", length=64, nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $scope;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="is_default", type="boolean", nullable=false)
	 */
	private $isDefault = '0';

	/**
	 * @var string
	 *
	 * @ORM\Column(name="description", type="text", nullable=true)
	 */
	private $description;


	/**
	 * Get scope
	 *
	 * @return string
	 */
	public function getScope() {
		return $this->scope;
	}

	/**
	 * Set isDefault
	 *
	 * @param boolean $isDefault
	 * @return OAuthScope
	 */
	public function setIsDefault($isDefault) {
		$this->isDefault = $isDefault;

		return $this;
	}

	/**
	 * Get isDefault
	 *
	 * @return boolean
	 */
	public function getIsDefault() {
		return $this->isDefault;
	}

	/**
	 * Set description
	 *
	 * @param string $description
	 * @return OAuthScope
	 */
	public function setDescription($description) {
		$this->description = $description;

		return $this;
	}

	/**
	 * Get description
	 *
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}
}
