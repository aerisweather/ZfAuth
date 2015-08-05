<?php


namespace Aeris\ZfAuthTest\Storage;


use Aeris\ZfAuth\Storage\ChainedStorage;
use Zend\Authentication\Storage\NonPersistent;

class ChainedStorageTest extends \PHPUnit_Framework_TestCase {

	/** @test */
	public function read_shouldReturnTheFirstNonNullResult() {
		$chainedStorage = new ChainedStorage([
			self::Store(null),
			self::Store('Alice'),
			self::Store('Bob')
		]);

		$this->assertEquals('Alice', $chainedStorage->read());
	}

	/** @test */
	public function read_shouldReturnFalsyResults() {
		$this->assertEquals(false, (new ChainedStorage([
			self::Store(null),
			self::Store(false),
			self::Store('foo')
		]))->read());

		$this->assertEquals(-1, (new ChainedStorage([
			self::Store(null),
			self::Store(-1),
			self::Store('foo')
		]))->read());
	}

	/** @test */
	public function read_shouldReturnNullIfAllStoresReturnNull() {
		$chainedStorage = new ChainedStorage([
			self::Store(null),
			self::Store(null),
			self::Store(null)
		]);

		$this->assertNull($chainedStorage->read());
	}

	/** @test */
	public function read_shouldReturnNullWhenNoStores() {
		$this->assertNull((new ChainedStorage([]))->read());
	}

	/** @test */
	public function isEmpty_shouldReturnTrueIfAllAreEmpty() {
		$this->assertTrue((new ChainedStorage([
			self::Store(null),
			self::Store(null),
			self::Store(null),
		]))->isEmpty());
	}

	/** @test */
	public function isEmpty_shouldReturnTrueWhenNoStores() {
		$this->assertTrue((new ChainedStorage([]))->isEmpty());
	}

	/** @test */
	public function isEmpty_shouldReturnFalseIfAnyAreNotEmpty() {
		$this->assertTrue((new ChainedStorage([
			self::Store(null),
			self::Store('foo'),
			self::Store(null),
		]))->isEmpty());
	}

	/** @test */
	public function isEmpty_shouldReturnFalseIfAnyHaveFalsyContents() {
		$this->assertTrue((new ChainedStorage([
			self::Store(null),
			self::Store(false),
			self::Store(null),
		]))->isEmpty());

		$this->assertTrue((new ChainedStorage([
			self::Store(null),
			self::Store(-1),
			self::Store(null),
		]))->isEmpty());
	}


	protected static function Store($data) {
		$store = new NonPersistent();
		$store->write($data);

		return $store;
	}

}