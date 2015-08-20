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

ZfAuth attempts to authenticate requests using a set of `IdentityProviders`. By default, users can be authenticated as:

* User implementing `IdentityInterface`, as configured in `zf_auth.authentication.user_entity_class` (a request with an `access_token`)
* `\Aeris\ZfAuth\Identity\OAuthClientIdentity` (a request with only client_id/client_secret)
* `\Aeris\ZfAuth\Identity\AnonymousIdentity` (a request with no authentication keys)

### Handling invalid credentials

If a request contains authentication credentials, but the identity provider is unable to provide an identity -- eg. the request contains an invalid/expired `access_token` -- an `MvcEvent::EVENT_DISPATCH_ERROR` event will be triggered, containing an `\Aeris\ZfAuth\Exception\AuthenticationException`. 

This can be handled by whatever view mechanism you wish. If you're using `Aeris\ZendRestModule`, you would handle `AuthenticationExceptions` in your `errors` config:

```php
return [
	'zend_rest' => [
		'errors' => [
			// ...
			[
				'error' => '\Aeris\ZfAuth\Exception\AuthenticationException',
				'http_code' => 401,
				'application_code' => 'authentication_error',
				'details' => 'The request failed to be authenticated. Check your access keys, and try again.'
			]
		]
	]
]
```

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

	public function canAuthenticate() {
		/** @var Request $request */
        $request = $this->serviceLocator->get('Application')
            ->getMvcEvent()
            ->getRequest();
		
		return $request->getQuery('super_secret_password') !== null;
	}

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
	  	// Override default identity provider
		  'Aeris\ZfAuth\IdentityProvider' => [
				// Wrap in ChainedIdentityProvider, so we still
				// have access to other authenticators
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

## Authorization

ZfAuth provides two ways to restrict resource access to authorized identities:

1. Route Guards
2. Voters

Route guards allow you to restrict access to a resource before a request has made it to a controller, using a simple rule set. Voters allow you to restrict access to a *specific resource*, using advanced logic.

### Route Guards

After a route has been matched to a controller, but before the controller action executes, ZfAuth will check your route guard rules, to see if the current identity passes each rule.

#### Configuration

Route guards are configured using the `zf_auth.guards` module option. Each key is the name of a guard service, and the value is an array of rules to apply to the guard.

```php
return [
	'zf_auth' => [
		'guards' => [
			'Aeris\ZfAuth\Guard\ControllerGuard' => [
				[
					'controller' => 'Aeris\ZfAuthTest\Controller\IndexController',
					'actions' => ['*'],
					'roles' => ['*']
				],
				[
					'controller' => 'Aeris\ZfAuthTest\Controller\AdminController',
					'actions' => ['get', 'getList', 'update', 'foo' ],
					'roles' => ['admin']
				],
			],
		]
	]
]
```

This example config would let any user access any action in the `IndexController`, but only let users with an `admin` role access `get`, `getList`, `update`, and `fooAction` methods on the `AdminController`. 

Note that any controller/action which is not configured will **be restricted by default.**

#### `ControllerGuard`

The `Aeris\ZfAuth\Guard\ControllerGuard` restricts access to controller actions based on the requesting user's role. 

The options are:

* `'controller'` The controller for which this rule applies (`ControllerManager` service name)
* `'actions'` The actions for which this rule applies. Use `'*'` to apply this rule to all actions of the controller. Note that to use `REST` actions, you must be using `Aeris\ZendRestModule\Mvc\Router\Http\RestSegment` route types (from `Aeris\ZendRestModule`)
* `'roles'` The roles which are allowed access to this controller action. Use `'*'` to allow any role.

#### Custom Guards

You can create a custom guard, which implements the `GuardInterface`:

```php
namespace Aeris\ZfAuth\Guard;

use Zend\Mvc\Router\RouteMatch;

interface GuardInterface {

	public function __construct(array $rules = []);

	public function setRules(array $rules);

	/** @return boolean */
	public function isGranted(RouteMatch $event);

}
```

The `isGranted` method should return true if the current identity is allowed to access the resource.

To demonstrate, let's make a guard that restricts users based on their username. Our final configuration will look like this:


```php
[
	'zf_auth' => [
		'guards' => [
			'MyApp\Guard\UsernameGuard' => [
				// Rules to pass to our guard
				[
					'controller' => 'MyApp\Controller\AdminController',
					'usernames' => ['alice', 'bob']
				],
				[
					'controller' => 'MyApp\Controller\IndexController',
					'usernames' => ['*']
				],
			]
		]
	]
]
```

Our `UsernameGuard` class will check the current controller and user identity against the rules provided in the configuration:

```php
class UsernameGuard implements GuardInterface {

	/** @var array  */
	protected $rules;

	/** @var IdentityProviderInterface */
	protected $identityProvider;

	public function __construct(array $rules = []) {
		$this->setRules($rules);
	}

	public function setRules(array $rules) {
		$this->rules = $rules;
	}

	/** @return boolean */
	public function isGranted(RouteMatch $routeMatch) {
		$controller = $routeMatch->getParam('controller');

		// Find usernames allowed for this controller
		$allowedUsernames = array_reduce($this->rules, function($allowed, $rule) use ($controller) {
			$isMatch = $rule['controller'] === $controller;
			return array_merge($allowed, $isMatch ? $rule['usernames'] : []);
		}, []);

		$username = $this->identityProvider->getIdentity()->getUsername();
		return in_array('*', $allowedUsernames) || in_array($username, $allowedUsernames);
	}

	public function setIdentityProvider(IdentityProviderInterface $identityProvider) {
		$this->identityProvider = $identityProvider;
	}
}
```

The last step is to register your guard with the ZfAuth guard manager:

```php
[
	'guard_manager' => [
		// Using Aeris\ZfDiConfig, because I'm fancy
		// but you can use service factories if you want to be lame
		'di' => [
			'MyApp\Guard\UsernameGuard' => [
				'class' => '\MyApp\Guard\UsernameGuard',
				'setters' => [
					'identityProvider' => '@Aeris\ZfAuth\IdentityProvider'
				]
			]
		]
	]
]
```

## Voters

Voters allow you to restrict access to specific resources. 

### Using Voters

The primary way to use voters is via the `AuthService`. Here's an example of how you might use the `AuthService` in a controller:

```php
use Aeris\ZfAuth\Service\AuthServiceAwareInterface;
use Zend\Mvc\Controller\AbstractRestfulController;

class AnimalRestController extends AbstractRestfulController implements AuthServiceAwareInterface {
	use \Aeris\ZfAuth\Service\AuthServiceAwareTrait;

	public function create($data) {
		$animal = new Animal($data);

		// Check if the current identity is allowed to create this animal
		if (!$this->authService->isGranted('create', $animal)) {
			throw new AuthorizationException('Tsk tsk tsk, you cannot create an animal, you!');
		}

		$this->persist($animal);
		return $animal;
	}
}
```

Notice that this controller implements `Aeris\ZfAuth\Service\AuthServiceAwareInterface` -- this will cause the controller to be automatically injected with the `AuthService\Aeris\ZfAuth\Service\AuthService` service by the ZF2 `ControllerManager`.

You can also grab the AuthService from the application service locator: `$serviceLocator->get('AuthService\Aeris\ZfAuth\Service\AuthService')`

### How Voters Work

A Voter is a class implementing `\Symfony\Component\Security\Core\Authorization\Voter\VoterInterface`. The `Voter::vote()` method returns either:

* `VoterInterface::ACESS_GRANTED`
* `VoterInterface::ACESS_DENIED`
* `VoterInterface::ACCESS_ABSTAIN`

When you call `AuthService::isGranted($action, $resource)`, the auth service runs through each registered voter, and collects votes. If any voter returns `ACCESS_DENIED`, then `isGranted()` will return false. 

### Implementing Custom Voters

Let's work off of the `AnimalRestController::create()` example from above. And let's say Mr. Boss Man gave us two rules that we must enforce:

1. Only logged in OAuth users may create animals
2. If you want to create a monkey, you must first *be* a monkey.

For these two rules, we will create two different voters:

```php
class OnlyUsersCanCreateAnimalsVoter implements VoterInterface {

	public function vote(TokenInterface $token, $resource, array $actions) {
		// First, we need to decide whether we care about this resource/action
		$doWeCare = $this->supportsClass(get_class($resource)) &&
			Aeris\Fn\any($actions, [$this, 'supportsAttribute']);

		if (!$doWeCare) {
			// Returning ACCESS_ABSTAIN tells our AuthService to ignore
			// the results of this voter
			return self::ACCESS_ABSTAIN;
		}

		// We can get the current Identity from the $token argument
		$currentIdentity = $token->getUser();

		$isLoggedInUser = !($currentIdentity instanceof \Aeris\ZfAuth\Identity\AnonymousIdentity);

		// Do not allow anonymous requests to create animals
		return $isLoggedInUser ? self::ACCESS_GRANTED : self::ACCESS_DENIED;
	}

	public function supportsAttribute($action) {
		// This voter only cares about `create` actions (aka "attributes")
		return $action === 'create';
	}

	public function supportsClass($class) {
		// This voter only cares about `Animal` objects
		return $class === 'MyApp\Model\Animal' || is_a($class, 'MyApp\Model\Animal');
	}
}

class OnlyMonkeysCanCreateMonkeysVoter implements VoterInterface {

	public function vote(TokenInterface $token, $resource, array $actions) {
		// Again, we need to decide whether we care about this resource/action
		$doWeCare = $this->supportsClass(get_class($resource)) &&
			Aeris\Fn\any($actions, [$this, 'supportsAttribute']) &&
			// And in this case, we only care about animals which are also monkeys
			$resource->getType() === 'monkey';

		if (!$doWeCare) {
			// Returning ACCESS_ABSTAIN tells our AuthService to ignore
			// the results of this voter
			return self::ACCESS_ABSTAIN;
		}

		// The $token is simply a Symfony interface which wraps a ZfAuth IdentityInterface object
		$currentIdentity = $token->getUser();

		$isCurrentIdentityAMonkey = $currentIdentity instanceof Animal && $currentIdentity->getType() === 'monkey';

		return $isCurrentIdentityAMonkey ? self::ACCESS_GRANTED : self::ACCESS_DENIED;
	}

	public function supportsAttribute($action) {
		// This voter only cares about `create` attribues (aka "actions")
		return $action === 'create';
	}

	public function supportsClass($class) {
		// This voter only cares about `Animal` objects
		return $class === 'MyApp\Model\Animal' || is_a($class, 'MyApp\Model\Animal');
	}
}
```

Finally, we need to register these voters, using the `zf_auth.voter_manager` config:

```php
[
	'voter_manager' => [
		'invokables' => [
			'OnlyUsersCanCreateAnimalsVoter' => '\MyApp\Voter\OnlyUsersCanCreateAnimalsVoter',
			'OnlyMonkeysCanCreateMonkeysVoter' => '\MyApp\Voter\OnlyMonkeysCanCreateMonkeysVoter'
		]
	]
];
```


### Voter Configuration Reference

```php
[
	'zf_auth' => [
		// Register voters here
		'voter_manager' => [
			// Accepts same config as `service_manager`
			'di' => [
				// Also accepts Aeris\ZfDiConfig
			]
		],
		'voter_options' => [
			// `strategy` can be one of:
			// - 'affirmative': grant access as soon as any voter returns ACCESS_GRANTED
			// - 'consensus': grant access if there are more voters granting access than there are denying
			// - 'unanimous' (default): only grant access if none of the voters has denied access
			'strategy' => 'unanimous',
			'allow_if_all_abstain' => true,
		]
	]
]
```
