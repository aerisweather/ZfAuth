<?php

namespace Aeris\ZfAuthTest\Functional\Authentication;


use Aeris\ZfAuth\Identity\OAuthClientIdentity;
use Aeris\ZfAuth\IdentityProvider\IdentityProviderInterface;
use Aeris\ZfAuthTest\Fixture\Entity\User;
use Aeris\ZfAuthTest\Functional\FunctionalTestCase;
use OAuth2\Request as OAuthRequest;
use Mockery as M;

class IdentityProvidersTest extends FunctionalTestCase {

	/** @var Object[] */
	protected $fixtures;

	protected function setUp() {
		parent::setUp();

		$this->fixtures = $this->loadFixtures(__DIR__ . '/fixtures/oauth.yml');
	}

	/** @test */
	public function shouldAuthenticateOAuthUsersViaAccessToken() {
		$this->dispatch('/safe-route', 'GET', [
			'access_token' => $this->requestAccessToken([
				'username' => 'testUser',
				'password' => 'testPass',
			])
		]);
		$user = $this->getCurrentIdentity();

		$this->assertInstanceOf('\Aeris\ZfAuthTest\Fixture\Entity\User', $user);
		$this->assertEquals($user->getId(), $this->fixtures['testUser']->getId());
	}

	/** @test */
	public function shouldAuthenticateAsAnonymousForInvalidAccessToken() {
		$this->dispatch('/safe-route', 'GET', [
			'access_token' => 'not_a_valid_access_token',
		]);

		$this->assertInstanceOf('\Aeris\ZfAuth\Identity\AnonymousIdentity', $this->getCurrentIdentity());
	}

	/** @test */
	public function shouldAuthenticateOAuthClientUsersWithNoAccessToken() {
		$this->dispatch('/safe-route', 'GET', [
			'client_id' => 'testClient',
			'client_secret' => 'testSecret'
		]);

		/** @var OAuthClientIdentity $identity */
		$identity = $this->getCurrentIdentity();
		$this->assertInstanceOf('\Aeris\ZfAuth\Identity\OAuthClientIdentity', $identity);
		$this->assertEquals('testClient', $identity->getClientId(), 'Should set the clientId');
	}

	/** @test */
	public function shouldAuthenticateAsAnonymousForInvalidClientKeys() {
		$this->dispatch('/safe-route', 'GET', [
			'client_id' => 'notAClientId',
			'client_secret' => 'notAClientSecret'
		]);

		$this->assertInstanceOf('\Aeris\ZfAuth\Identity\AnonymousIdentity', $this->getCurrentIdentity());
	}

	/** @test */
	public function shouldAuthenticateAsAnonymousForNoClientKeysOrToken() {
		$this->dispatch('/safe-route', 'GET');

		$this->assertInstanceOf('\Aeris\ZfAuth\Identity\AnonymousIdentity', $this->getCurrentIdentity());
	}

	/** @return User */
	private function getCurrentIdentity() {
		/** @var IdentityProviderInterface $identityProvider */
		$identityProvider = $this->getApplicationServiceLocator()->get('Aeris\ZfAuth\IdentityProvider');
		/** @var User $user */
		$user = $identityProvider->getIdentity();
		return $user;
	}


}