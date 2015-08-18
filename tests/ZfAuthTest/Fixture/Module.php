<?php


namespace Aeris\ZfAuthTest\Fixture;


class Module {

	public function getConfig() {
		return [
			'controllers' => [
				'invokables' => [
					'Aeris\ZfAuthTest\Controller\IndexController' => '\Aeris\ZfAuthTest\Fixture\Controller\IndexController',
					'Aeris\ZfAuthTest\Controller\AdminController' => '\Aeris\ZfAuthTest\Fixture\Controller\AdminController',
				],
			],

			'router' => [
				'routes' => [
					'index' => [
						'type' => 'Zend\Mvc\Router\Http\Segment',
						'options' => [
							'route' => '/',
							'defaults' => [
								'controller' => 'Aeris\ZfAuthTest\Controller\IndexController',
								'action' => 'index'
							]
						]
					],
					'admin-rest' => [
						'type' => 'Aeris\ZendRestModule\Mvc\Router\Http\RestSegment',
						'options' => [
							'route' => '/admin[/:id]',
							'defaults' => [
								'controller' => 'Aeris\ZfAuthTest\Controller\AdminController',
							]
						]
					],
					'admin-foo-action' => [
						'type' => 'Zend\Mvc\Router\Http\Segment',
						'options' => [
							'route' => '/admin/fooAction',
							'defaults' => [
								'controller' => 'Aeris\ZfAuthTest\Controller\AdminController',
								'action' => 'foo'
							]
						]
					]
				]
			],

			'service_manager' => [
				'di' => [
					'Aeris\ZfAuthTest\IdentityProvider\TestIdentityProvider' => '\Aeris\ZfAuthTest\Fixture\IdentityProvider\IdentityProvider',
					'Aeris\ZfAuth\IdentityProvider' => [
						'class' => 'Aeris\ZfAuth\IdentityProvider\ChainedIdentityProvider',
						'setters' => [
							'providers' => [
								'@Aeris\ZfAuthTest\IdentityProvider\TestIdentityProvider',
								'@Aeris\ZfAuth\IdentityProvider\OAuthUserIdentityProvider',
								'@Aeris\ZfAuth\IdentityProvider\OAuthClientIdentityProvider',
								'@Aeris\ZfAuth\IdentityProvider\AnonymousIdentityProvider'
							]
						]
					]
				]
			]
		];
	}

}