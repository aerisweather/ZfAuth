<?php


namespace Aeris\ZfAuth\Service;


use Aeris\ZfAuth\Identity\IdentityInterface;

interface AuthServiceInterface {

	public function isGranted($permission, $resource = null);

	/** @return IdentityInterface */
	public function getIdentity();
}