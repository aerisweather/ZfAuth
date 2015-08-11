<?php

namespace Aeris\ZfAuth\Repository;
use Aeris\ZfAuth\Identity\IdentityInterface;


/**
 * Adapter for retrieving an Identity for an IdentityStorage.
 */
interface IdentityRepositoryInterface {

	/**
	 * @param $id
	 * @return IdentityInterface Identity object
	 */
	public function findById($id);

	/**
	 * @param string $username
	 * @return IdentityInterface
	 */
	public function findByUsername($username);

}