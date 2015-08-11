<?php


namespace Aeris\ZfAuthTest\Voter;


use Aeris\ZendRestModuleTest\RestTestModule\Model\Animal;
use Aeris\ZfAuth\Identity\IdentityInterface;
use Aeris\ZfAuth\Voter\ResourceLimitVoter;
use Aeris\ZfAuthTest\Helper\Spy;
use Mockery as M;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface as Voter;

class ResourceLimitVoterTest extends \PHPUnit_Framework_TestCase {

	/** @test */
	public function shouldAbstainForUnsupportedResources() {
		$voter = new ResourceLimitVoter([
			'supportedClass' => '\Aeris\ZendRestModuleTest\RestTestModule\Model\Animal',
			'supportsResource' => Spy::returns(false),
			'limit' => Spy::returns(100),
			'count' => Spy::returns(50),
		]);

		$result = $voter->vote(self::Token(), new Animal(), ['create']);

		$this->assertEquals(Voter::ACCESS_ABSTAIN, $result);
	}

	/** @test */
	public function shouldAbstainForActionsOtherThanCreate() {
		$voter = new ResourceLimitVoter([
			'supportedClass' => '\Aeris\ZendRestModuleTest\RestTestModule\Model\Animal',
			'supportsResource' => Spy::returns(true),
			'limit' => Spy::returns(100),
			'count' => Spy::returns(50),
		]);

		$result = $voter->vote(self::Token(), new Animal(), ['update']);
		$this->assertEquals(Voter::ACCESS_ABSTAIN, $result);
	}

	/** @test */
	public function shouldAbstainIfTheVoterDoesNotSupportTheClass() {
		$voter = new ResourceLimitVoter([
			'supportedClass' => '\DateTime',
			'supportsResource' => Spy::returns(true),
			'limit' => Spy::returns(100),
			'count' => Spy::returns(50),
		]);

		$result = $voter->vote(self::Token(), new Animal(), ['create']);

		$this->assertEquals(Voter::ACCESS_ABSTAIN, $result);
	}


	/** @test */
	public function shouldCallSupportsResourceWithResource() {
		$voter = new ResourceLimitVoter([
			'supportedClass' => '\Aeris\ZendRestModuleTest\RestTestModule\Model\Animal',
			'supportsResource' => $supportsResource = Spy::returns(true),
			'limit' => Spy::returns(100),
			'count' => Spy::returns(50),
		]);

		$voter->vote(self::Token(), $resource = new Animal(), ['create']);

		$supportsResource
			->shouldHaveBeenCalled()
			->with($resource);
	}

	/** @test */
	public function shouldAllowCreatingAResourceIfItWillNotExceedLimit() {
		$voter = new ResourceLimitVoter([
			'supportedClass' => '\Aeris\ZendRestModuleTest\RestTestModule\Model\Animal',
			'supportsResource' => Spy::returns(true),
			'limit' => Spy::returns(100),
			'count' => Spy::returns(99)
		]);

		$result = $voter->vote(self::Token(), new Animal(), ['create']);

		$this->assertEquals(Voter::ACCESS_GRANTED, $result);
	}

	/** @test */
	public function shouldAllowCreatingAResourceIfTheLimitIsNegativeOne() {
		$voter = new ResourceLimitVoter([
			'supportedClass' => '\Aeris\ZendRestModuleTest\RestTestModule\Model\Animal',
			'supportsResource' => Spy::returns(true),
			'limit' => Spy::returns(-1),
			'count' => Spy::returns(9999999)
		]);

		$result = $voter->vote(self::Token(), new Animal(), ['create']);

		$this->assertEquals(Voter::ACCESS_GRANTED, $result);
	}

	/** @test */
	public function shouldNotAllowCreateAResourceWhichExceedTheLimit() {
		$voter = new ResourceLimitVoter([
			'supportedClass' => '\Aeris\ZendRestModuleTest\RestTestModule\Model\Animal',
			'supportsResource' => Spy::returns(true),
			'limit' => Spy::returns(100),
			'count' => Spy::returns(101)
		]);

		$result = $voter->vote(self::Token(), new Animal(), ['create']);

		$this->assertEquals(Voter::ACCESS_DENIED, $result);
	}

	/** @test */
	public function shouldNotAllowCreatingAResourceWhenLimitIsAlreadyMet() {
		$voter = new ResourceLimitVoter([
			'supportedClass' => '\Aeris\ZendRestModuleTest\RestTestModule\Model\Animal',
			'supportsResource' => Spy::returns(true),
			'limit' => Spy::returns(100),
			'count' => Spy::returns(100)
		]);

		$result = $voter->vote(self::Token(), new Animal(), ['create']);

		$this->assertEquals(Voter::ACCESS_DENIED, $result);
	}

	/** @test */
	public function shouldNotAllowCreatingAResourceWhenLimitIsZero() {
		$voter = new ResourceLimitVoter([
			'supportedClass' => '\Aeris\ZendRestModuleTest\RestTestModule\Model\Animal',
			'supportsResource' => Spy::returns(true),
			'limit' => Spy::returns(0),
			'count' => Spy::returns(0)
		]);

		$result = $voter->vote(self::Token(), new Animal(), ['create']);

		$this->assertEquals(Voter::ACCESS_DENIED, $result);
	}

	/** @test */
	public function shouldCallLimitAndCountWithUserObject() {
		$voter = new ResourceLimitVoter([
			'supportedClass' => '\Aeris\ZendRestModuleTest\RestTestModule\Model\Animal',
			'supportsResource' => Spy::returns(true),
			'limit' => $limit = Spy::returns(100),
			'count' => $count = Spy::returns(200),
		]);

		$user = self::Identity(['foo']);
		$voter->vote(self::Token(['getUser' => $user]), new Animal(), ['create']);

		$isUser = function($arg) {
			return in_array('foo', $arg->getRoles());
		};

		$limit
			->shouldHaveBeenCalled()
			->with(M::on($isUser));
		$count
			->shouldHaveBeenCalled()
			->with(M::on($isUser));
	}

	/** @return M\Mock|IdentityInterface */
	public static function Identity(array $roles = []) {
		return M::mock('\Aeris\ZfAuth\Identity\IdentityInterface', [
			'getRoles' => $roles,
		]);
	}

	/** @return M\Mock|TokenInterface */
	public static function Token($mocks = []) {
		$mocks = array_replace([
			'getUser' => self::Identity(),
		], $mocks);
		return M::mock('\Symfony\Component\Security\Core\Authentication\Token\TokenInterface', $mocks);
	}

}