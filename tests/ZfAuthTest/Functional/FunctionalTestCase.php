<?php

namespace Aeris\ZfAuthTest\Functional;

use Aeris\ZendRestModuleTest\AbstractTestCase;


class FunctionalTestCase extends AbstractTestCase {
	public function __construct($name = NULL, array $data = array(), $dataName = '') {
		parent::__construct($name, $data, $dataName);
		$this->appConfigDir = __DIR__ . '/../../config';
	}

	protected function requestAccessToken(array $requestParams = []) {
		$requestParams = array_replace([
			'client_id' => 'testclient',
			'client_secret' => 'testpass',
			'username' => 'test_username',
			'password' => 'test_password',
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
}