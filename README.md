# ZfAuth

Authentication/Authorization components for Zend Framework 2.

### Install

Install with composer

```
composer require aeris/zf-auth
```

Add module to your `application.config.php`

```php
return [
  'modules' => [
	  'Aeris\ZfAuth',
      
      // REQUIRED Dependencies
      'Aeris\ZfDiConfig',      // for fancy service manager config
      
      // OPTIONAL Dependencies
      'Zf\OAuth2',             // if using OAuth IdentityProviders
      'Zf\ContentNegotiation', // if using OAuth IdentityProviders
      'DoctrineModule',        // if using DoctrineOrmIdentityRepository
      'DoctrineORMmodule',     // if using DoctrineOrmIdentityRepository
      'ZfcRbac',               // if using Route Guards
  ]
];

// Note that unless you're customizing Zf\OAuth2 services, 
// you probably will need all of the "optional" modules.
```

### Configuration Reference

```php
return [
    // See https://github.com/zfcampus/zf-oauth2/blob/master/config/oauth2.local.php.dist
	'zf-oauth2' => [...],
	// See https://github.com/doctrine/DoctrineORMModule/blob/master/config/module.config.php
	'doctrine' => [...],
	
	// Aeris\ZfAuth configuration
	'zf_auth' => [
		'authentication' => [
		    // If you're using a Doctrine Entity as a user identity,
		    // supply the entity class here (required for DoctrineOrmIdentityRepository).
			'user_entity_class' => 'Path\To\Entity\User'
		]
	]
]
```

### OAuth2 Database Setup

If your using the `Zf\OAuth2` module, you will need to create database tables for oauth storage. See `/tests/data/zf-oauth-test.sql` for an example MySQL oauth db schema.

`Aeris\ZfAuth` has a set of Doctrine entities which map to the oauth database tables, located under the `Aeris\ZfAuth\Entity` namespace. 

You can see sample configuration files for wiring up `Zf\OAuth2`, and `DoctrineOrmModule` in `/tests/config/autoload/`

## Authentication


### Identity Providers

ZfAuth authenticates requests via Identity Providers, which expose `IdentityInterface` objects. An identity provider can be wrapped as a ZF2 service, and injected into controllers, authorization services, etc.

The default ZfAuth identity provider authenticates users from access tokens using the `Zf\OAuth` module, and returns a user of the type defined in the `zf_auth.authentication.user_entity_class` config.

The default identity provider is a `ChainedIdentityProvider`, which means that it will attempt to return an identity from a collection of identity providers, returning the first identity provided. An call to `getIdentity()` will look like:

* Find user associated with the requested `access_token`
* If no user is found, find a `\Aeris\ZfAuth\Identity\OAuthClientIdentity` associated with the requested `client_id`/`client_secret`
* If no user is found, return an `\Aeris\ZfAuth\Identity\AnonymousIdentity` instance


#### Usage Example

```php
$identityProvider = $serviceLocator->get('Aeris\ZfAuth\IdentityProvider');
$user = $identityProvider->getIdentity();

// See "Authorization" docs for a more advanced approach to authorization.
if (in_array('admin', $user->getRoles()) {
  $this->doLotsOfCoolThings();
}
else {
  throw new UnauthorizedUserException();
}
```


#### Custom Identity Providers

Let's say we have a super-special user, with a super-special static password, which let's them do super-special things. Here's how we might go about authenticating that user.

```php
use Aeris\ZfAuth\IdentityProvider\IdentityProviderInterface;
use Zend\Http\Request;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class SuperSpecialIdentityProvider implements IdentityProviderInterface, ServiceLocatorAwareInterface {
	use \Zend\ServiceManager\ServiceLocatorAwareTrait;

	/** @return \Aeris\ZfAuth\Identity\IdentityInterface */
	public function getIdentity() {
		/** @var Request $request */
		$request = $this->serviceLocator->get('Application')
			->getMvcEvent()
			->getRequest();

		$password = $request->getQuery('super_secret_password');
		$isSuperSecretUser = $password === '42';

		// Return null if we cannot authenticate the user
		if ($isSuperSecretUser) {
			return null;
		}

		// Return our super-secret user
		return $this->serviceLocator
			->get('entity_manager')
			->getRepo('MyApp\Entity\User')
			->findOneByUsername('superSecretUser');
	}
}
```


Now let's wire it up.

```php
// module.config.php
return [
  'service_manager' => [
	  // Aeris\ZfDiConfig ftw
	  'di' => [
		  // Wrap in ChainedIdentityProvider, so we still
		  // have access to other authenticators
		  'IdentityProvider' => [
			  'class' => 'Aeris\ZfAuth\IdentityProvider\ChainedIdentityProvider',
			  'setters' => [
				  'providers' => [
						// Add our provider to the top of the list
						'$factory:\MyApp\IdentityProviders\SuperSpecialIdentityProvider'
						// Include default set of providers	             
						'@Aeris\ZfAuth\IdentityProvider\OAuthUserIdentityProvider',
						'@Aeris\ZfAuth\IdentityProvider\OAuthClientIdentityProvider',
						'@Aeris\ZfAuth\IdentityProvider\AnonymousIdentityProvider'
				  ]
			  ]
		  ]
	  ]
  ]
];
```