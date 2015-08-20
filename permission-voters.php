<?php
namespace Notes\PermissionVoters;

use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;

$conf = [
	'route_guards' => [

	],
	'roles' => [
		// Not at all authenticated -- any rando request
		'anonymous' => [
			''
		],
		// Has been authenticated as an oauth client, but no user is logged in
		'client' => [

		],
		// base role for a logged-in user
		'user' => [
			'resources' => [
				'alert' => [
					'view' => ['local'],
					'create' => ['local']
				]
			],
		],
		// Admin for the entire Turbine application domain
		'admin' => [
			'resource' => [
				'alert' => [
					'view' => ['global'],
					'create' => ['global']
				],
				'user' => [
					'delete' => ['notSelf']
				]
			]
		]
	],


	'route_guards' => [
		'\AlertRestController::update' => 'alert.update'
	]
];

// maybe better than 'local'/'global'
// would be:
// - 'own' (belongs to user)
// - 'local' (belongs to same oauth client)
// - 'global' (belongs to a different oauth client)
//
// Having a separate 'local' for oauth clients
//  would allow for separate `oauth_client_admin' role

class Permission {
	public $action = 'view';
	public $resource = 'alert';
	public $modifier = 'global';
}


class BelongsToUserVoter extends AbstractPermissionVoter {
	protected function supportsPermission(Permission $p) {
		return $p->modifier === 'local';
	}

	protected function isGrantedPermission(Permission $p, $object, $user = null) {
		if (!$user) {
			return false;
		}

		if (!($object instanceof BelongsToUserInterface)) {
			throw new \InvalidArgumentException('Expected object of type' . get_class($object) . ' to implement BelongsToUserInterface');
		}

		return $object->belongsToUser($user);
	}
}

class NotSelfVoter extends AbstractPermissionVoter {
	protected function supportsPermission(Permission $permission) {
		return $permission->modifier === 'notSelf' && $permission->resource === 'user';
	}

	protected function isGrantedPermission(Permission $permission, $object, $user) {
		return $object->getUsername() !== $user->getUsername();
	}
}



abstract class AbstractPermissionVoter {

	abstract protected function supportsPermission(Permission $permission);

	abstract protected function isGrantedPermission(Permission $permission, $object, $user);

	public function vote(TokenInterface $token, $object, array $actions) {
		if (!$object) {
			return self::ACCESS_ABSTAIN;
		}
		$objectResource = $this->getResourceName($object);


		$userPermission = $this->permissionProvider->getUserPermissions($user);

		// Find user permissions which relate to this vote
		$applicablePermissions = array_filter($userPermission, function ($p) use ($actions, $objectResource) {
			return $p->resource === $objectResource && in_array($p->action, $actions);
		});

		// Find user permissions which are supported by this voter
		$supportedPermissions = array_filter($applicablePermissions, [$this, 'supportsPermission']);


		// No permissions are supported by this voter
		// --> abstain
		if (!count($supportedPermissions)) {
			return self::ACCESS_ABSTAIN;
		}

		$isGranted = F::all($supportedPermissions, function (Permission $p) {
			return $this->isGrantedPermission($p, $resource, $user);
		});

		return $isGranted ? self::ACCESS_GRANTED : self::ACCESS_DENIED;
	}
}