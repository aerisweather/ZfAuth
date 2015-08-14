<?php


namespace Aeris\ZfAuthTest\Functional\Authorization;


use Aeris\ZfAuthTest\Fixture\IdentityProvider\IdentityProvider;
use Aeris\ZfAuthTest\Functional\FunctionalTestCase;

class RouteGuardsTest extends FunctionalTestCase {

	/** @var IdentityProvider */
	protected $identityProvider;

	protected function setUp() {
		parent::setUp();
		$this->identityProvider = new IdentityProvider();
		$this->useServiceMock('Aeris\ZfAuth\IdentityProvider', $this->identityProvider);
	}

	/** @test */
	public function shouldAcceptRequestsFromUsersWithAppropriateRoles() {
		$this->identityProvider->setIdentityRoles(['admin']);
		$this->dispatch('/admin/fooAction', 'GET');

		$this->assertResponseStatusCode(200);
	}

	/** @test */
	public function shouldRejectRequestsFromUsersWithoutAppropriateRoles() {
		$this->identityProvider->setIdentityRoles(['regular_ol_user']);
		$this->dispatch('/admin/fooAction', 'GET');

		$this->assertResponseStatusCode(403);
	}

	/** @test */
	public function shouldAcceptRequestsFromUsersWithAppropriateRoles_restAction() {
		$this->identityProvider->setIdentityRoles(['admin']);
		$this->dispatch('/admin/123', 'DELETE');

		$this->assertResponseStatusCode(200);
	}

	/** @test */
	public function shouldRejectRequestsFromUsersWithoutAppropriateRoles_restAction() {
		$this->identityProvider->setIdentityRoles(['regular_ol_user']);
		$this->dispatch('/admin/123', 'DELETE');

		$this->assertResponseStatusCode(403);
	}

}