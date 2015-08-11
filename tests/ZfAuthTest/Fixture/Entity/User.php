<?php

namespace Aeris\ZfAuthTest\Fixture\Entity;


use Aeris\ZfAuth\Identity\IdentityInterface;
use Aeris\ZfAuth\Service\Encryptor;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="user")
 */
class User implements IdentityInterface {

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="username", type="string", length=254, nullable=false)
	 */
	private $username;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="password", type="string", length=60, nullable=false)
	 */
	private $password;

	protected $roles = ['user'];

	/**
	 * User constructor.
	 */
	public function __construct() {
		$this->encryptor = new Encryptor();
	}



	/**
	 * Set username
	 *
	 * @param string $username
	 * @return User
	 */
	public function setUsername($username) {
		$this->username = $username;

		return $this;
	}

	/**
	 * Get username
	 *
	 * @return string
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * Set password
	 *
	 * @param $rawPassword
	 * @return User
	 */
	public function setPassword($rawPassword) {
		$this->password    = $this->encryptor->encrypt($rawPassword);
		$this->passwordRaw = $rawPassword;
		return $this;
	}

	/**
	 * @param $rawPassword
	 * @return bool
	 */
	public function checkPassword($rawPassword) {
		return $this->encryptor->verify($rawPassword, $this->password);
	}

	/**
	 * Get password
	 *
	 * @return string
	 */
	public function getPassword() {
		return $this->password;
	}

	public function setRoles(array $roles) {
		$this->roles = $roles;
	}

	public function getRoles() {
		return $this->roles;
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}
}