<?php

namespace Aeris\ZfAuth\Service;


trait AuthServiceAwareTrait {

	/** @var AuthServiceInterface */
	protected $authService;

	public function setAuthService(AuthServiceInterface $authService) {
		$this->authService = $authService;
	}
}