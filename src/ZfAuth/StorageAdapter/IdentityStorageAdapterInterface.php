<?php

namespace Aeris\ZfAuth\StorageAdapter;


/**
 * Adapter for retrieving an Identity for an IdentityStorage.
 */
interface IdentityStorageAdapterInterface {

	/**
	 * @param $id
	 * @return mixed Identity object
	 */
	public function findById($id);

	public function findByUsername($username);

}