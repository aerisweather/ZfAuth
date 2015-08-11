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