<?php

namespace Aeris\ZfAuthTest\Guard;


use Aeris\ZfAuth\Guard\ControllerGuard;

use Aeris\ZfAuthTest\Fixture\IdentityProvider\IdentityProvider;
use Mockery as M;
use Zend\Mvc\Router\RouteMatch;

class ControllerGuardTest extends \PHPUnit_Framework_TestCase {

	/** @var IdentityProvider */
	protected $identityProvider;

	protected function setUp() {
		parent::setUp();

		$this->identityProvider = new IdentityProvider();
	}

	/** @test */
	public function shouldGrantToConfiguredRoles() {
		$guard = new ControllerGuard([
			[
				'controller' => 'TestController',
				'actions' => ['foo', 'bar', 'get', 'update'],
				'roles' => ['manager', 'super_duper_user']
			]
		]);
		$identityProvider = new IdentityProvider();
		$identityProvider->setIdentityRoles(['super_duper_user']);
		$guard->setIdentityProvider($identityProvider);

		$this->assertTrue($guard->isGranted(new RouteMatch([
			'controller' => 'TestController',
			'action' => 'bar',
		])), 'Should grant for actions');

		$this->assertTrue($guard->isGranted(new RouteMatch([
			'controller' => 'TestController',
			'restAction' => 'update',
		])), 'Should grant for rest actions');

		$this->assertFalse($guard->isGranted(new RouteMatch([
			'controller' => 'AnotherController',
			'restAction' => 'bar',
		])), 'Should not grant for non-configured controllers');

		$this->assertFalse($guard->isGranted(new RouteMatch([
			'controller' => 'TestController',
			'restAction' => 'notAnAction',
		])), 'Should not grant for non-configured actions');

		$this->assertFalse($guard->isGranted(new RouteMatch([
			'controller' => 'AnotherController',
			'restAction' => 'notAnAction',
		])), 'Should not grant for non-configured controllers + actions');

	}

	/** @test */
	public function shouldNotGrantToUserWithoutConfiguredRoles() {
		$guard = new ControllerGuard([
			[
				'controller' => 'TestController',
				'actions' => ['foo', 'bar', 'get', 'update'],
				'roles' => ['manager', 'super_duper_user']
			]
		]);
		$identityProvider = new IdentityProvider();
		$identityProvider->setIdentityRoles(['lame_o_user']);
		$guard->setIdentityProvider($identityProvider);

		$this->assertFalse($guard->isGranted(new RouteMatch([
			'controller' => 'TestController',
			'action' => 'bar',
		])), 'Should grant for actions');
	}


	protected static function Identity(array $roles) {
		return M::mock('\Aeris\ZfAuth\Identity\IdentityInterface', [
			'getRoles' => $roles
		]);
	}

	// Then, do a functional test, just
	// to make sure we grant / reject in basic use case
}
