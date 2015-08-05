<?php


namespace Aeris\ZfAuth\Service;

use ZF\OAuth2\Adapter\BcryptTrait;

class Encryptor implements EncryptorInterface {
	use BcryptTrait;

	public function encrypt($password) {
		return $this->getBcrypt()->create($password);
	}

	public function verify($str, $hash) {
		return $this->verifyHash($str, $hash);
	}
} 