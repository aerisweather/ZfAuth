<?php
return [
	'zf_auth' => [
		'authentication' => [
			// Override this to implement your own custom user class.
			'user_entity_class' => '\YourApp\Entity\User'
		]
	],
	'service_manager' => [
		'factory' => [
			'OAuth2\Request' => '\Aeris\ZfAuth\Factory\OAuth2RequestFactory'
		],
		'di' => [
			'Aeris\ZfAuth\IdentityStorageAdapter' => [
				'class' => '\Aeris\ZfAuth\StorageAdapter\DoctrineOrmUserStorageAdapter',
				'args' => [
					'entityManager' => '@entity_manager',
					'userEntityClass' => '%zf_auth.authentication.user_entity_class'
				]
			],
			'Aeris\ZfAuth\Storage\OAuthUserStorage' => [
				'class' => '\Aeris\ZfAuth\Storage\OAuthUserStorage',
				'setters' => [
					'request' => '@OAuth2\Request',
					'oauthServer' => '@ZF\OAuth2\Service\OAuth2Server',
					'identityStorageAdapter' => '@Aeris\ZfAuth\IdentityStorageAdapter',
				]
			],
			'Aeris\ZfAuth\Storage\OAuthClientIdentityStorage' => [
				'class' => '\Aeris\ZfAuth\Storage\OAuthUserStorage',
				'setters' => [
					'request' => '@OAuth2\Request',
					'oauthServer' => '@ZF\OAuth2\Service\OAuth2Server'
				]
			],
			'Aeris\ZfAuth\Storage\AnonymousIdentityStorage' => '\Aeris\ZfAuth\Storage\AnonymousIdentityStorage',
			'Aeris\ZfAuth\Storage\IdentityStorage' => [
				'class' => '\Aeris\ZfAuth\Storage\ChainedStorage',
				'args' => [
					$storageChain = [
						'@Aeris\ZfAuth\Storage\OAuthUserStorage',
						'@Aeris\ZfAuth\Storage\OAuthClientIdentityStorage',
						'@Aeris\ZfAuth\Storage\AnonymousIdentityStorage'
					]
				]
			]
		]
	]
];