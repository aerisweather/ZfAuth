<?php

namespace Aeris\ZfAuthTest\Storage;

use Aeris\ZfAuth\Storage\OAuthUserStorage;
use \Mockery as M;
use Zend\Http\Request;
use Zend\Stdlib\Parameters;

class OAuthUserStorageTest extends \PHPUnit_Framework_TestCase {

	/** @var M\Mock; */
	protected $oauthServer;
	/** @var M\Mock */
	protected $identityStorageAdapter;
	/** @var Request */
	protected $request;
	/** @var OAuthUserStorage */
	protected $storage;

	protected function setUp() {
		parent::setUp();
		$this->oauthServer = M::mock('\OAuth2\Server');
		$this->identityStorageAdapter = M::mock('\Aeris\ZfAuth\StorageAdapter\IdentityStorageAdapterInterface');
		$this->request = new Request();
		$this->storage = new OAuthUserStorage();

		$this->storage->setOAuthServer($this->oauthServer);
		$this->storage->setIdentityStorageAdapter($this->identityStorageAdapter);
		$this->storage->setRequest($this->request);
	}

	protected function tearDown() {
		parent::tearDown();

		M::close();
	}



	/** @test */
	public function read_shouldReturnTheUserCorrespondingToTheAccessToken_inQuery() {
		$this->request->setQuery(new Parameters([
			'access_token' => 'at123'
		]));

		$this->oauthServer->shouldReceive('getAccessTokenData')
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
		$this->request->setQuery(new Parameters([]));

		$this->assertNull($this->storage->read());
	}

}
