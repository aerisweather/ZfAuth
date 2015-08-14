<?php
use Aeris\ZendRestModule\Event\RestErrorEvent;

return [
	'zend_rest' => [
		'cache_dir' => __DIR__ . '/../../data/cache',

		'errors' => [
			[
				'error'           => \Zend\Mvc\Application::ERROR_ROUTER_NO_MATCH,
				'http_code'        => 404,
				'application_code' => 'invalid_request',
				'details'         => 'The requested endpoint or action is invalid and not supported.',
			],
			[
				'error' => '\Aeris\ZfAuth\Exception\AuthenticationException',
				'http_code' => 401,
				'application_code' => 'authentication_error',
				'details' => 'The request failed to be authenticated. Check your access keys, and try again.'
			],
			[
				'error' => '\Aeris\ZfAuth\Exception\AuthorizationExceptionInterface',
				'http_code' => 403,
				'application_code'=> 'authorization_error',
				'details' => 'The user is not authorized to access this resource.'
			],
			[
				'error' => '\Exception',
				'http_code' => 500,
				'application_code' => 'invalid_request',
				'details' => 'The requested endpoint or action is invalid and not supported.',
				'on_error' => function (RestErrorEvent $evt) {
					$ex = $evt->getError();
					die((string)$ex);
				}
			]
		]
	]
];