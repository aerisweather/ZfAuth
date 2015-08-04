<?php


namespace Aeris\ZfAuth\StorageAdapter;


use Doctrine\ORM\EntityManagerInterface;

class DoctrineOrmUserStorageAdapter implements IdentityStorageAdapterInterface {

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
}