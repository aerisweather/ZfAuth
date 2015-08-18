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
		'guards' => [
			'Aeris\ZfAuth\Guard\ControllerGuard' => [
				[
					'controller' => 'ZF\OAuth2\Controller\Auth',
					'actions' => 'token',
					'roles' => ['oauth_client']
				]
			]
		]
	],
	'controllers' => [
		'initializers' => [
			'\Aeris\ZfAuth\Initializer\AuthServiceAwareInitializer',
		],
	],
	'service_manager' => [
		'factories' => [
			'OAuth2\Request' => '\Aeris\ZfAuth\Factory\OAuth2RequestFactory',
			'OAuth2\Server' => '\Aeris\ZfAuth\Factory\OAuth2ServerFactory',
			// Do not use ZfDiConfig here
			// --> if a consuming application tries to
			//		 override the IdentityProvider,
			//		 ZF2 will instead merge the two configs together,
			// 		 resulting in each component provider being set twice.
			'Aeris\ZfAuth\IdentityProvider' => '\Aeris\ZfAuth\Factory\IdentityProviderFactory',
		],
		'initializers' => [
			'\Aeris\ZfAuth\Initializer\AuthServiceAwareInitializer',
		],
		'di' => array_replace(
			include __DIR__ . '/identity-providers.config.php',
			[
				'Aeris\ZfAuth\Service\AuthService' => [
					'class' => '\Aeris\ZfAuth\Service\AuthService',
					'setters' => [
						'identityProvider' => '@Aeris\ZfAuth\IdentityProvider',
					],
				],
				'Aeris\ZfAuth\PluginManager\GuardManager' => [
					'$serviceManager' => [
						'service_type' => '\Aeris\ZfAuth\Guard\GuardInterface',
						'config' => '%zf_auth.guard_manager'
					]
				],
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