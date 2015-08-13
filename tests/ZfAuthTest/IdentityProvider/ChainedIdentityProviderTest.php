<?php

namespace Aeris\ZfAuthTest\IdentityProvider;


use Aeris\ZfAuth\Identity\IdentityInterface;
use Aeris\ZfAuth\IdentityProvider\ChainedIdentityProvider;
use Aeris\ZfAuth\IdentityProvider\IdentityProviderInterface;
use Mockery as M;

class ChainedIdentityProviderTest extends \PHPUnit_Framework_TestCase {

	/** @test */
	public function canAuthenticate_shouldReturnTrueIfAnyProviderCanAuthenticate() {
		$provider = new ChainedIdentityProvider([
			self::IdentityProvider(null, false),
			self::IdentityProvider(self::Identity(), true),
			self::IdentityProvider(null, false),
		]);

		$this->assertTrue($provider->canAuthenticate());
	}

	/** @test */
	public function canAuthenticate_shouldReturnTrueEvenIfTheProviderReturnsANullIdentity() {
		$provider = new ChainedIdentityProvider([
			self::IdentityProvider(null, false),
			self::IdentityProvider(null, true),
			self::IdentityProvider(null, false),
		]);

		$this->assertTrue($provider->canAuthenticate());
	}

	/** @test */
	public function canAuthenticate_shouldReturnFalseIfNoProviderCanAuthenticate() {
		$provider = new ChainedIdentityProvider([
			self::IdentityProvider(null, false),
			self::IdentityProvider(null, false),
			self::IdentityProvider(null, false),
		]);

		$this->assertFalse($provider->canAuthenticate());
	}

	/** @test */
	public function canAuthenticate_shouldReturnFalseIfNoProviders() {
		$provider = new ChainedIdentityProvider([]);

		$this->assertFalse($provider->canAuthenticate());
	}

	/** @test */
	public function getIdentity_shouldReturnTheIdentityFromTheFirstProviderWhoCanAuthenticate() {
		$provider = new ChainedIdentityProvider([
			self::IdentityProvider(self::Identity(), false),
			self::IdentityProvider($idFromFirstAble = self::Identity(), true),
			self::IdentityProvider(self::Identity(), true),
		]);

		$this->assertSameIdentity($idFromFirstAble, $provider->getIdentity());
	}


	/** @test */
	public function getIdentity_shouldReturnNullIfTheFirstProviderWhoCanAuthenticateReturnsNull() {
		$provider = new ChainedIdentityProvider([
			self::IdentityProvider(self::Identity(), false),
			self::IdentityProvider(null, true),
			self::IdentityProvider(self::Identity(), true),
		]);

		$this->assertEquals(null, $provider->getIdentity());
	}

	/** @test */
	public function getIdentity_shouldReturnNullWhenNoProviders() {
		$provider = new ChainedIdentityProvider([]);
		$this->assertEquals(null, $provider->getIdentity());
	}


	/**
	 * @return M\MockInterface|IdentityInterface
	 */
	protected static function Identity() {
		return M::mock('\Aeris\ZfAuth\Identity\IdentityInterface', [
			'getRoles' => [],
			'getTestId' => uniqid('id_test_'),			// for Test::assertSameIdentity()
		]);
	}

	protected function assertSameIdentity($expectedIdentity, $actualIdentity, $msg = 'Failed asserting the the two identities are the same.') {
		// because doing `assertSame` against a mockery object is slow.
		return $this->assertEquals($expectedIdentity->getTestId(), $actualIdentity->getTestId(), $msg);
	}

	protected function assertNullIdentity($identity, $msg = 'Failed asserting that identity is null.') {
		$this->assertEquals(null, $identity, $msg);	// because `assertNull` is slow on Mockery objects
	}

	/**
	 * @param IdentityInterface|M\Mock $identity
	 * @return M\Mock|IdentityProviderInterface
	 */
	protected static function IdentityProvider($identity = null, $canAuthenticate = true) {
		return M::mock('\Aeris\ZfAuth\IdentityProvider\IdentityProviderInterface', [
			'getIdentity' => $identity,
			'canAuthenticate' => $canAuthenticate
		]);
	}
}
