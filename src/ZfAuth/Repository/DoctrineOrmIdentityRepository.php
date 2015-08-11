<?php

namespace Aeris\ZfAuth\Repository;


use Aeris\ZfAuth\Repository\IdentityRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineOrmIdentityRepository implements IdentityRepositoryInterface {

	/** @var EntityManagerInterface  */
	protected $entityManager;

	/** @var @string Class name of the user identity */
	protected $userEntityClass;

	/**
	 * @param $id
	 * @return mixed Identity object
	 */
	public function findById($id) {
		return $this->entityManager
			->getRepository($this->userEntityClass)
			->find($id);
	}

	/**
	 * @param $username
	 * @return object Identity object
	 */
	public function findByUsername($username) {
		return $this->entityManager
			->getRepository($this->userEntityClass)
			->findOneBy([
				'username' => $username
			]);
	}

	/**
	 * @param EntityManagerInterface $entityManager
	 */
	public function setEntityManager($entityManager) {
		$this->entityManager = $entityManager;
	}

	/**
	 * @param string $userEntityClass (FQCN)
	 */
	public function setUserEntityClass($userEntityClass) {
		$this->userEntityClass = $userEntityClass;
	}
}