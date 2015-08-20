<?php


namespace Aeris\ZfAuthTest\Functional\Authorization;


use Aeris\ZfAuth\Service\AuthServiceInterface;
use Aeris\ZfAuthTest\Functional\FunctionalTestCase;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Zend\ModuleManager\ModuleEvent;
use Zend\Stdlib\ArrayUtils;
use Aeris\Fn;
use Mockery as M;

class VotersTest extends FunctionalTestCase {

	/** @var int */
	protected $monkeyCount;

	/** @var int */
	protected $monkeyLimit;

	/** @var AuthServiceInterface */
	protected $authService;

	/** @var VoterInterface|M\Mock */
	protected $voter;

	protected function setUp() {
		$this->voter = M::mock('\Symfony\Component\Security\Core\Authorization\Voter\VoterInterface', [
			'supportsClass' => true,
			'supportsAttribute' => true
		]);

		parent::setUp();

		$this->authService = $this->useService('Aeris\ZfAuth\Service\AuthService');
	}

	protected function getTestModuleConfig() {
		return [
			'zf_auth' => [
				'voter_manager' => [
					'factories' => [
						'SomeVoter' => function() {
							return $this->voter;
						}
					],
				]
			]
		];
	}


	/** @test */
	public function shouldDenyAccessIfVoterDenies() {
		$this->voter->shouldReceive('vote')
			->andReturn(VoterInterface::ACCESS_DENIED);

		$this->assertFalse($this->authService->isGranted('whatevs'));
	}


	/** @test */
	public function shouldGrantAccessTokenIfVoterGrants() {
		$this->voter->shouldReceive('vote')
			->andReturn(VoterInterface::ACCESS_GRANTED);

		$this->assertTrue($this->authService->isGranted('whatevs'));
	}

	/** @test */
	public function shouldGrantAccessTokenIfVoterAbstains() {
		$this->voter->shouldReceive('vote')
			->andReturn(VoterInterface::ACCESS_GRANTED);

		$this->assertTrue($this->authService->isGranted('whatevs'));
	}


}