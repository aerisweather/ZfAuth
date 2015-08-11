<?php


namespace Aeris\ZfAuthTest\IdentityProvider;

use Aeris\ZfAuth\IdentityProvider\OAuthUserIdentityProvider;
use \Mockery as M;
use OAuth2\Request;


class OAuthUserIdentityProviderTest extends \PHPUnit_Framework_TestCase {
	/** @var M\Mock; */
	protected $oauthServer;
	/** @var M\Mock */
	protected $identityRepository;
	/** @var Request */
	protected $oauthRequest;
	/** @var OAuthUserIdentityProvider */
	protected $identityProvider;

	protected function setUp() {
		parent::setUp();
		$this->oauthServer = M::mock('\OAuth2\Server');
		$this->identityRepository = M::mock('\Aeris\ZfAuth\Repository\IdentityRepositoryInterface');
		$this->oauthRequest = new Request();
		$this->identityProvider = new OAuthUserIdentityProvider();

		$this->identityProvider->setOAuthServer($this->oauthServer);
		$this->identityProvider->setIdentityAdapter($this->identityRepository);
		$this->identityProvider->setRequest($this->oauthRequest);
	}

	protected function tearDown() {
		parent::tearDown();

		M::close();
	}



	/** @test */
	public function getIdentity_shouldReturnTheUserCorrespondingToTheAccessToken_inQuery() {
		$this->oauthRequest->query['access_token'] = 'at123';

		$this->oauthServer->shouldReceive('getAccessTokenData')
			->with($this->oauthRequest)
			->andReturn([
				'user_id' => 'jimmy'
			]);

		$identity = M::mock('\Aeris\ZfAuth\Identity\IdentityInterface');
		$this->identityRepository->shouldReceive('findByUsername')
			->with('jimmy')
			->andReturn($identity);

		$this->assertSame($identity, $this->identityProvider->getIdentity());
	}


	/** @test */
	public function getIdentity_shouldReturnNullIfThereIsNoAccessToken() {
		$this->assertNull($this->identityProvider->getIdentity());
	}
}