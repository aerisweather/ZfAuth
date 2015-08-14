<?php


namespace Aeris\ZfAuthTest\Guard;


use Aeris\ZfAuth\Guard\AggregateRouteGuard;
use Aeris\ZfAuthTest\Fixture\ServiceManager\ServiceLocator;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\ServiceManager\ServiceLocatorInterface;
use Mockery as M;

class AggregateRouteGuardTest extends \PHPUnit_Framework_TestCase {

	/** @var ServiceLocatorInterface */
	protected $routeGuardManager;

	protected function setUp() {
		parent::setUp();

		$this->routeGuardManager = new ServiceLocator();
		$this->routeGuardManager->set('AlwaysGrantedGuard', M::mock('\Aeris\ZfAuth\Guard\GuardInterface', [
			'isGranted' => true,
			'setRules' => null,
		]));
		$this->routeGuardManager->set('NeverGrantedGuard', M::mock('\Aeris\ZfAuth\Guard\GuardInterface', [
			'isGranted' => false,
			'setRules' => null
		]));
	}

	/** @test */
	public function isGranted_shouldSetRulesOnGuards() {
		$guard = new AggregateRouteGuard();
		$guard->setGuardManager($this->routeGuardManager);
		$guard->setRules([
			'AlwaysGrantedGuard' => [
				'foo' => 'bar'
			],
			'NeverGrantedGuard' => [
				'faz' => 'baz'
			]
		]);

		$guard->isGranted(new RouteMatch([]));

		$this->routeGuardManager->get('AlwaysGrantedGuard')
			->shouldHaveReceived('setRules')
			->once()
			->with(['foo' => 'bar']);

		$this->routeGuardManager->get('NeverGrantedGuard')
			->shouldHaveReceived('setRules')
			->once()
			->with(['faz' => 'baz']);
	}

	/** @test */
	public function isGranted_shouldPassRouteMatchToComponentGuards() {
		$guard = new AggregateRouteGuard();
		$guard->setGuardManager($this->routeGuardManager);
		$guard->setRules([
			'AlwaysGrantedGuard' => [],
			'NeverGrantedGuard' => []
		]);


		$routeMatch = new RouteMatch([ 'foo' => 'bar']);
		$guard->isGranted($routeMatch);

		$isSameRouteMatch = function(RouteMatch $rm) {
			return $rm->getParam('foo') === 'bar';
		};

		$this->routeGuardManager->get('AlwaysGrantedGuard')
			->shouldHaveReceived('isGranted')
			->once()
			->with(M::on($isSameRouteMatch));

		$this->routeGuardManager->get('NeverGrantedGuard')
			->shouldHaveReceived('isGranted')
			->once()
			->with(M::on($isSameRouteMatch));
	}

	/** @test */
	public function isGranted_shouldFailIfAnyComponentGuardFails() {
		$guard = new AggregateRouteGuard();
		$guard->setGuardManager($this->routeGuardManager);
		$guard->setRules([
			'AlwaysGrantedGuard' => [],
			'NeverGrantedGuard' => []
		]);

		$this->assertFalse($guard->isGranted(new RouteMatch([])));
	}

	/** @test */
	public function isGranted_shouldPassIfAllComponentGuardPasses() {
		$guard = new AggregateRouteGuard();
		$guard->setGuardManager($this->routeGuardManager);
		$guard->setRules([
			'AlwaysGrantedGuard' => [],
		]);

		$this->assertTrue($guard->isGranted(new RouteMatch([])));
	}

	/** @test */
	public function isGranted_shouldFailIfNoRulesAreSet() {
		$guard = new AggregateRouteGuard();
		$guard->setGuardManager($this->routeGuardManager);

		$this->assertFalse($guard->isGranted(new RouteMatch([])));
	}

}