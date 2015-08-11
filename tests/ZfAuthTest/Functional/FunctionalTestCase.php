<?php

namespace Aeris\ZfAuthTest\Functional;

use Aeris\ZendRestModuleTest\AbstractTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\Alice;


class FunctionalTestCase extends AbstractTestCase {
	public function __construct($name = NULL, array $data = array(), $dataName = '') {
		parent::__construct($name, $data, $dataName);
		$this->appConfigDir = __DIR__ . '/../../config';
	}

	protected function setUp() {
		parent::setUp();
		$this->purgeDb();
	}

	protected function requestAccessToken(array $requestParams = []) {
		$requestParams = array_replace([
			'client_id' => 'testClient',
			'client_secret' => 'testSecret',
			'username' => 'testUser',
			'password' => 'testPass',
			'grant_type' => 'password'
		], $requestParams);


		$this->dispatch('/oauth', 'POST', $requestParams);
		$this->assertResponseStatusCode(200);

		try {
			$responseData = json_decode($this->getResponse()->getContent());
			$accessToken = $responseData->access_token;
		}
		catch (\Exception $error) {
			throw new \Exception('Failed to get access code: ' . $error->getMessage());
		}
		return $accessToken;
	}

	protected function purgeDb() {
		/** @var EntityManagerInterface $entityManager */
		$entityManager = $this->getEntityManager();
		$sm = $entityManager->getConnection()->getSchemaManager();
		$tables = $sm->listTableNames();

		$entityManager->getConnection()->exec('SET foreign_key_checks = 0');
		foreach ($tables as $name) {
			$entityManager->getConnection()->executeUpdate("TRUNCATE $name");
		}
		$entityManager->getConnection()->exec('SET foreign_key_checks = 1');
	}

	protected function loadFixtures($path) {
		$fixtures = Alice\Fixtures::load($path, $this->getEntityManager());
		$this->getEntityManager()->flush();

		return $fixtures;
	}

	/** @return EntityManagerInterface */
	protected function getEntityManager() {
		return $this->getApplicationServiceLocator()
			->get('doctrine.entitymanager.orm_default');
	}
}