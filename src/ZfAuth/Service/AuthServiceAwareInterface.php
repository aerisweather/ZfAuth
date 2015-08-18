<?php


namespace Aeris\ZfAuth\Service;


interface AuthServiceAwareInterface {

	public function setAuthService(AuthServiceInterface $authService);

}