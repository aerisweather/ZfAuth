<?php
return [
	'zf_auth' => [
		'authentication' => [
			'user_entity_class' => '\Aeris\ZfAuthTest\Fixture\Entity\User'
		],
		'guards' => [
			'Aeris\ZfAuth\Guard\ControllerGuard' => [
				[
					'controller' => 'Aeris\ZfAuthTest\Controller\IndexController',
					'actions' => ['*'],
					'roles' => ['*']
				],
				[
					'controller' => 'Aeris\ZfAuthTest\Controller\AdminController',
					'actions' => ['*'],
					'roles' => ['admin']
				],
			],
		]
	]
];