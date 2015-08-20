# v1.0.0

Initial Release of Aeris\ZfAuth.

Includes:

* Authentication using `IdentityProviderInterface` objects
* Default `IdentityProvider` service, using OAuth2
* Route Guards, for restricting access to controller actions based on request identity's role
* `Aeris\ZfAuth\AuthService` service, which uses `VoterInterface`s to restrict access to resources
* Docs :)