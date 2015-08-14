<?php
return [
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
];