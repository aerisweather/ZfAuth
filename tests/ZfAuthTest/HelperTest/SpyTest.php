<?php


namespace Aeris\ZfAuthTest\HelperTest;


use Aeris\ZfAuthTest\Helper\Spy;
use Mockery as M;

class SpyTest extends \PHPUnit_Framework_TestCase {

	public function tearDown() {
		M::close();
	}

	/** @test */
	public function shouldHaveBeenCalled_shouldPassIfTheSpyWasCalled() {
		$spy = new Spy();

		$spy();

		$spy->shouldHaveBeenCalled();
	}

	/** @test */
	public function shouldHaveBeenCalled_shouldPassIfTheSpyWasCalled_withArgs() {
		$spy = new Spy();

		$spy('foo');

		$spy->shouldHaveBeenCalled()
			->with('foo');
	}

	/**
	 * @test
	 * @expectedException \Mockery\Exception\InvalidCountException
	 */
	public function shouldHaveBeenCalled_shouldFailIfTheSpyWasNotCalled() {
		$spy = new Spy();

		$spy->shouldHaveBeenCalled();
	}

	/**
	 * @test
	 * @expectedException \Mockery\Exception\InvalidCountException
	 */
	public function shouldHaveBeenCalled_shouldFailIfTheSpyWasCalled_withWrongArgs() {
		$spy = new Spy();

		$spy('foo');

		$spy->shouldHaveBeenCalled()
			->with('bar');
	}

	/** @test */
	public function shouldNotHaveBeenCalled_shouldPassIfSpyWasNotCalled() {
		$spy = new Spy();

		$spy->shouldNotHaveBeenCalled();
	}
	/**
	 * @test
	 * @expectedException \Mockery\Exception\InvalidCountException
	 */
	public function shouldNotHaveBeenCalled_shouldFailIfSpyWasCalled() {
		$spy = new Spy();

		$spy();

		$spy->shouldNotHaveBeenCalled();
	}

	/** @test */
	public function andReturn_shouldReturnTheValue() {
		$spy = new Spy();
		$spy->andReturn('foo');

		$this->assertEquals('foo', $spy());
	}

	/** @test */
	public function andReturnUsing_shouldReturnACallbacksValue() {
		$spy = new Spy();

		$spy->andReturnUsing(function($arg) {
			return strtoupper($arg);
		});

		$this->assertEquals('FOO', $spy('foo'));
	}

	/** @test */
	public function returns_shouldCreateASpyWhichReturnTheValue() {
		$spy = Spy::returns('foo');

		$this->assertEquals('foo', $spy());
	}

}