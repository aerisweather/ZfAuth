<?php


namespace Aeris\ZfAuth\Repository;


use Aeris\ZfAuth\Entity\ResourceLimitInterface;
use Aeris\ZfAuth\Identity\IdentityInterface;

interface ResourceLimitRepositoryInterface {

	/**
	 * @param IdentityInterface $identity
	 * @param string $key
	 * @return ResourceLimitInterface|null
	 */
	public function findByIdentity(IdentityInterface $identity, $key);

}