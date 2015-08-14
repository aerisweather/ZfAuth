<?php
$obj = new \Aeris\ZfAuth\Repository\DoctrineOrmIdentityRepository();
return [
	'zf_auth' => [
		'authentication' => [
			// Override this to implement your own custom user class.
			'user_entity_class' => '\YourApp\Entity\User'
		]
	],
	'service_manager' => [
		'factories' => [
			'OAuth2\Request' => '\Aeris\ZfAuth\Factory\OAuth2RequestFactory',
			'OAuth2\Server' => '\Aeris\ZfAuth\Factory\OAuth2ServerFactory',
		],
		'di' => array_replace(
			include __DIR__ . '/identity-providers.config.php',
		[])
	]
];