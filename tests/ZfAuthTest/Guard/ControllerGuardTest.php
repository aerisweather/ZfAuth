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
	public function shouldGrantToAnyRolesWithAsterisk() {
		$guard = new ControllerGuard([
			[
				'controller' => 'TestController',
				'actions' => ['foo', 'get'],
				'roles' => ['*']
			]
		]);
		$identityProvider = new IdentityProvider();
		$identityProvider->setIdentityRoles(['whatever_user']);
		$guard->setIdentityProvider($identityProvider);

		$this->assertTrue($guard->isGranted(new RouteMatch([
			'controller' => 'TestController',
			'action' => 'foo',
		])), 'Should grant for actions');

		$this->assertTrue($guard->isGranted(new RouteMatch([
			'controller' => 'TestController',
			'restAction' => 'get',
		])), 'Should grant for rest actions');
	}

	/** @test */
	public function shouldGrantToAnyActionWithAsterisk() {
		$guard = new ControllerGuard([
			[
				'controller' => 'TestController',
				'actions' => ['*'],
				'roles' => ['admin']
			]
		]);
		$identityProvider = new IdentityProvider();
		$identityProvider->setIdentityRoles(['admin']);
		$guard->setIdentityProvider($identityProvider);

		$this->assertTrue($guard->isGranted(new RouteMatch([
			'controller' => 'TestController',
			'action' => 'foo',
		])), 'Should grant for actions');

		$this->assertTrue($guard->isGranted(new RouteMatch([
			'controller' => 'TestController',
			'restAction' => 'get',
		])), 'Should grant for rest actions');
	}

	/** @test */
	public function shouldNotPrioritizeActionWildcards() {
		$guard = new ControllerGuard([
			[
				'controller' => 'TestController',
				'actions' => ['*'],
				'roles' => ['*']
			],
			[
				'controller' => 'TestController',
				'actions' => ['secretAction'],
				'roles' => ['admin']
			],
		]);
		$identityProvider = new IdentityProvider();
		$identityProvider->setIdentityRoles(['user']);
		$guard->setIdentityProvider($identityProvider);

		$this->assertTrue($guard->isGranted(new RouteMatch([
			'controller' => 'TestController',
			'action' => 'whatever'
		])), 'Should grant wildcard-ed actions');

		$this->assertFalse($guard->isGranted(new RouteMatch([
			'controller' => 'TestController',
			'action' => 'secretAction'
		])), 'But should prioritize explicit rules');
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
