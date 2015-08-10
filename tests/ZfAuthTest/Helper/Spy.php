<?php


namespace Aeris\ZfAuthTest\Helper;

use Mockery as M;

class Spy {

	/** @var M\Mock  */
	protected $mock;

	const SPY_METHOD = '__SPY__';

	/**
	 * Spy constructor.
	 */
	public function __construct() {
		$this->mock = M::spy('\stdClass');
	}

	public function __invoke() {
		$args = func_get_args();
		return call_user_func_array([$this->mock, self::SPY_METHOD], $args);
	}

	public static function returns($val) {
		$spy = new Spy();

		$spy->andReturn($val);

		return $spy;
	}

	/**
	 * @return M\Expectation
	 */
	public function shouldHaveBeenCalled() {
		return $this->mock->shouldHaveReceived(self::SPY_METHOD);
	}

	public function shouldNotHaveBeenCalled() {
		return $this->mock->shouldNotHaveReceived(self::SPY_METHOD);
	}

	public function andReturn($val) {
		return $this->mock->shouldReceive(self::SPY_METHOD)
			->andReturn($val);
	}

	public function andReturnUsing(callable $using) {
		return $this->mock->shouldReceive(self::SPY_METHOD)
			->andReturnUsing($using);
	}

}