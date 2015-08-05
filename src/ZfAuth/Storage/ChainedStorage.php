<?php


namespace Aeris\ZfAuth\Storage;


use Zend\Authentication\Storage\StorageInterface;
use Aeris\Fn;

/**
 * Stores data in a chained series of storages.
 * Reads from the first storage to return a non-null value
 */
class ChainedStorage implements StorageInterface {

	/** @var StorageInterface[] */
	protected $stores;

	/**
	 * @param StorageInterface[] $stores
	 */
	public function __construct(array $stores = []) {
		$this->stores = $stores;
	}


	public function isEmpty() {
		return count($this->stores) ? Fn\any($this->stores, Fn\caller('isEmpty')) : true;
	}

	/**
	 * Returns the contents of storage
	 *
	 * Behavior is undefined when storage is empty.
	 *
	 * @throws \Zend\Authentication\Exception\ExceptionInterface If reading contents from storage is impossible
	 * @return mixed
	 */
	public function read() {
		$result = null;
		foreach ($this->stores as $storage) {
			$result = $storage->read();
			if ($result !== null) {
				break;
			}
		}

		return $result;
	}

	/**
	 * Writes $contents to storage
	 *
	 * @param  mixed $contents
	 * @throws \Zend\Authentication\Exception\ExceptionInterface If writing $contents to storage is impossible
	 * @return void
	 */
	public function write($contents) {
		throw new \Exception('ChainedStorage does not implement write');
	}

	/**
	 * Clears contents from storage
	 *
	 * @throws \Zend\Authentication\Exception\ExceptionInterface If clearing contents from storage is impossible
	 * @return void
	 */
	public function clear() {
		throw new \Exception('ChainedStorage does not implement clear');
	}

}