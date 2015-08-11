<?php
return [
	'zf-oauth2' => [
		'allow_implicit' => true, // default (set to true when you need to support browser-based or mobile apps)
		'access_lifetime' => 3600, // default (set a value in seconds for access tokens lifetime)
		'enforce_state' => true, // default
		'storage' => 'ZF\OAuth2\Adapter\PdoAdapter', // service name for the OAuth2 storage adapter
		'storage_settings' => [
			'client_table' => 'oauth_clients',
			'access_token_table' => 'oauth_access_tokens',
			'refresh_token_table' => 'oauth_refresh_tokens',
			'code_table' => 'oauth_authorization_codes',
			'user_table' => 'user',
			'jwt_table' => 'oauth_jwt',
			'scope_table' => 'oauth_scopes',
			'public_key_table' => 'oauth_public_keys',
		]
	]
];