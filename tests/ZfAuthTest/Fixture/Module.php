<?php


namespace Aeris\ZfAuthTest\Fixture;


class Module {

	public function getConfig() {
		return [
			'controllers' => [
				'invokables' => [
					'ZfAuthTest\Controller\IndexController' => '\Aeris\ZfAuthTest\Fixture\Controller\IndexController'
				],
			],

			'router' => [
				'routes' => [
					'index' => [
						'type' => 'Zend\Mvc\Router\Http\Segment',
						'options' => [
							'route' => '/',
							'defaults' => [
								'controller' => 'ZfAuthTest\Controller\IndexController',
								'action' => 'index'
							]
						]
					]
				]
			]
		];
	}

}