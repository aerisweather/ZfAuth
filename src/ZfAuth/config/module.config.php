<?php
$obj = new \Aeris\ZfAuth\Repository\DoctrineOrmIdentityRepository();
return [
	'zf_auth' => [
		'authentication' => [
			// Override this to implement your own custom user class.
			'user_entity_class' => '\YourApp\Entity\User'
		],
		'guard_manager' => [
			'di' => [
				'Aeris\ZfAuth\Guard\ControllerGuard' => [
					'class' => '\Aeris\ZfAuth\Guard\ControllerGuard',
					'setters' => ['identityProvider' => '@Aeris\ZfAuth\IdentityProvider']
				]
			]
		],
		'guards' => []
	],
	'service_manager' => [
		'factories' => [
			'OAuth2\Request' => '\Aeris\ZfAuth\Factory\OAuth2RequestFactory',
			'OAuth2\Server' => '\Aeris\ZfAuth\Factory\OAuth2ServerFactory',
			'Aeris\ZfAuth\PluginManager\GuardManager' => '\Aeris\ZfAuth\Factory\GuardManagerFactory',
		],
		'di' => array_replace(
			include __DIR__ . '/identity-providers.config.php',
			[
				'Aeris\ZfAuth\RouteGuard' => [
					'class' => '\Aeris\ZfAuth\Guard\AggregateRouteGuard',
					'setters' => [
						'guardManager' => '@Aeris\ZfAuth\PluginManager\GuardManager',
						'rules' => '%zf_auth.guards'
					]
				]
			]
		)
	]
];