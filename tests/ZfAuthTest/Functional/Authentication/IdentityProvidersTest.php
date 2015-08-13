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
		$accessToken = $this->requestAccessToken([
			'username' => 'testUser',
			'password' => 'testPass',
		]);
		$this->resetResponseObject();

		$this->dispatch('/', 'GET', [
			'access_token' => $accessToken
		]);
		$this->assertResponseStatusCode(200);

		$user = $this->getCurrentIdentity();

		$this->assertInstanceOf('\Aeris\ZfAuthTest\Fixture\Entity\User', $user);
		$this->assertEquals($user->getId(), $this->fixtures['testUser']->getId());
	}

	/** @test */
	public function shouldReturnErrorForBadAccessToken() {
		$this->dispatch('/', 'GET', [
			'access_token' => 'not_a_valid_access_token',
		]);

		$this->assertAuthenticationErrorResponse();
	}

	/** @test */
	public function shouldAuthenticateOAuthClientUsersWithNoAccessToken() {
		$this->dispatch('/', 'GET', [
			'client_id' => 'testClient',
			'client_secret' => 'testSecret'
		]);
		$this->assertResponseStatusCode(200);

		/** @var OAuthClientIdentity $identity */
		$identity = $this->getCurrentIdentity();
		$this->assertInstanceOf('\Aeris\ZfAuth\Identity\OAuthClientIdentity', $identity);
		$this->assertEquals('testClient', $identity->getClientId(), 'Should set the clientId');
	}

	/** @test */
	public function shouldReturnErrorForBadClientClients() {
		$this->dispatch('/', 'GET', [
			'client_id' => 'notAClientId',
			'client_secret' => 'notAClientSecret'
		]);

		$this->assertAuthenticationErrorResponse();
	}

	/** @test */
	public function shouldAuthenticateAsAnonymousForNoClientKeysOrToken() {
		$this->dispatch('/', 'GET');
		$this->assertResponseStatusCode(200);

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

	protected function assertAuthenticationErrorResponse() {
		// Note, this only happens as a JSON response,
		// because we've configured ZendRestModule to
		// handle \Aeris\ZfAuth\Exception\AuthenticationException's
		$this->assertResponseStatusCode(401);
		$this->assertJsonErrorCodeEquals('authentication_error');
	}

}