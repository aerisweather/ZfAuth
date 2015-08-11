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
		'di' => [
			'Aeris\ZfAuth\IdentityRepository' => [
				'class' => '\Aeris\ZfAuth\Repository\DoctrineOrmIdentityRepository',
				'setters' => [
					'entityManager' => '@doctrine.entitymanager.orm_default',
					'userEntityClass' => '%zf_auth.authentication.user_entity_class'
				]
			],
			'Aeris\ZfAuth\IdentityProvider\OAuthUserIdentityProvider' => [
				'class' => '\Aeris\ZfAuth\IdentityProvider\OAuthUserIdentityProvider',
				'setters' => [
					'request' => '@OAuth2\Request',
					'oauthServer' => '@OAuth2\Server',
					'identityAdapter' => '@Aeris\ZfAuth\IdentityRepository',
				]
			],
			'Aeris\ZfAuth\IdentityProvider\OAuthClientIdentityProvider' => [
				'class' => '\Aeris\ZfAuth\IdentityProvider\OAuthClientIdentityProvider',
				'setters' => [
					'request' => '@OAuth2\Request',
					'oauthServer' => '@OAuth2\Server'
				]
			],
			'Aeris\ZfAuth\IdentityProvider\AnonymousIdentityProvider' => '\Aeris\ZfAuth\IdentityProvider\AnonymousIdentityProvider',
			'Aeris\ZfAuth\IdentityProvider' => [
				'class' => 'Aeris\ZfAuth\IdentityProvider\ChainedIdentityProvider',
				'setters' => [
					'providers' => [
						'@Aeris\ZfAuth\IdentityProvider\OAuthUserIdentityProvider',
						'@Aeris\ZfAuth\IdentityProvider\OAuthClientIdentityProvider',
						'@Aeris\ZfAuth\IdentityProvider\AnonymousIdentityProvider'
					]
				]
			]
		]
	]
];