<?php


namespace Aeris\ZfAuthTest\Functional\Authentication;


use Aeris\ZfAuth\IdentityProvider\ChainedIdentityProvider;
use Aeris\ZfAuthTest\Fixture\IdentityProvider\IdentityProvider as TestIdentityProvider;
use Aeris\ZfAuthTest\Functional\FunctionalTestCase;

class CustomIdentityProvidersTest extends FunctionalTestCase {

	/** @test */
	public function shouldProvideACustomIdentity() {
		/** @var ChainedIdentityProvider $identityProvider */
		$identityProvider = $this->getApplicationServiceLocator()
			->get('Aeris\ZfAuth\IdentityProvider');

		/** @var TestIdentityProvider $testProvider */
		$testProvider = $this->getApplicationServiceLocator()
			->get('Aeris\ZfAuthTest\IdentityProvider\TestIdentityProvider');


		$this->assertInstanceOf('\Aeris\ZfAuth\Identity\AnonymousIdentity', $identityProvider->getIdentity(), 'baseline test');

		// enable test provider
		$testProvider->setCanAuthenticate(true);
		$testProvider->setIdentityRoles(['super_duper_role']);

		$this->assertContains('super_duper_role', $identityProvider->getIdentity()->getRoles());
	}

}