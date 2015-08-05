<?php

namespace Aeris\ZfAuthTest\Storage;

use Aeris\ZfAuth\Storage\OAuthUserStorage;
use \Mockery as M;
use OAuth2\Request;

class OAuthUserStorageTest extends \PHPUnit_Framework_TestCase {

	/** @var M\Mock; */
	protected $oauthServer;
	/** @var M\Mock */
	protected $identityStorageAdapter;
	/** @var Request */
	protected $oauthRequest;
	/** @var OAuthUserStorage */
	protected $storage;

	protected function setUp() {
		parent::setUp();
		$this->oauthServer = M::mock('\OAuth2\Server');
		$this->identityStorageAdapter = M::mock('\Aeris\ZfAuth\StorageAdapter\IdentityStorageAdapterInterface');
		$this->oauthRequest = new Request();
		$this->storage = new OAuthUserStorage();

		$this->storage->setOAuthServer($this->oauthServer);
		$this->storage->setIdentityStorageAdapter($this->identityStorageAdapter);
		$this->storage->setRequest($this->oauthRequest);
	}

	protected function tearDown() {
		parent::tearDown();

		M::close();
	}



	/** @test */
	public function read_shouldReturnTheUserCorrespondingToTheAccessToken_inQuery() {
		$this->oauthRequest->query['access_token'] = 'at123';

		$this->oauthServer->shouldReceive('getAccessTokenData')
			->with($this->oauthRequest)
			->andReturn([
				'user_id' => 'jimmy'
			]);

		$identity = M::mock('\Aeris\ZfAuth\Identity\IdentityInterface');
		$this->identityStorageAdapter->shouldReceive('findByUsername')
			->with('jimmy')
			->andReturn($identity);

		$this->assertSame($identity, $this->storage->read());
	}


	/** @test */
	public function read_shouldReturnNullIfThereIsNoAccessToken() {
		$this->assertNull($this->storage->read());
	}

}
