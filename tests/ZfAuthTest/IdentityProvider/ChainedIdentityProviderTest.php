<?php

namespace Aeris\ZfAuthTest\IdentityProvider;


use Aeris\ZfAuth\Identity\IdentityInterface;
use Aeris\ZfAuth\IdentityProvider\ChainedIdentityProvider;
use Aeris\ZfAuth\IdentityProvider\IdentityProviderInterface;
use Mockery as M;

class ChainedIdentityProviderTest extends \PHPUnit_Framework_TestCase {

	/** @test */
	public function getIdentity_shouldReturnTheFirstNonNullResult() {
		$chainedIdentityProvider = new ChainedIdentityProvider([
			self::IdentityProvider(null),
			self::IdentityProvider($alice = self::Identity()),
			self::IdentityProvider($bob = self::Identity())
		]);

		$this->assertSame($alice, $chainedIdentityProvider->getIdentity());
	}
	/** @test */
	public function getIdentity_shouldReturnTheFirstNonNullResult_firstResult() {
		$chainedIdentityProvider = new ChainedIdentityProvider([
			self::IdentityProvider($alice = self::Identity()),
			self::IdentityProvider(null),
			self::IdentityProvider($bob = self::Identity())
		]);

		$this->assertSame($alice, $chainedIdentityProvider->getIdentity());
	}


	/** @test */
	public function getIdentity_shouldReturnNullIfAllProvidersReturnNull() {
		$chainedIdentityProvider = new ChainedIdentityProvider([
			self::IdentityProvider(null),
			self::IdentityProvider(null),
			self::IdentityProvider(null)
		]);

		$this->assertNull($chainedIdentityProvider->getIdentity());
	}

	/** @test */
	public function getIdentity_shouldReturnNullWhenNoProviders() {
		$this->assertNull((new ChainedIdentityProvider([]))->getIdentity());
	}


	/**
	 * @return M\MockInterface|IdentityInterface
	 */
	protected static function Identity() {
		return M::mock('\Aeris\ZfAuth\Identity\IdentityInterface', [
			'getRoles' => []
		]);
	}

	/**
	 * @param IdentityInterface|M\Mock $identity
	 * @return M\Mock|IdentityProviderInterface
	 */
	protected static function IdentityProvider($identity = null) {
		return M::mock('\Aeris\ZfAuth\IdentityProvider\IdentityProviderInterface', [
			'getIdentity' => $identity
		]);
	}
}
