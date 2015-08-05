<?php

if (!file_exists(__DIR__ . '/doctrine.local.php')) {
	throw new \RuntimeException('Please create a local version of this file');
}

return [
	'doctrine' => [
		'driver' => [
			'annotation_driver' => [
				'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
				'cache' => 'array',
				'paths' => [
					__DIR__ . '/../../ZfAuthTest/Fixture',
					__DIR__ . '/../../../src/ZfAuth/Entity'
				]
			],
			'orm_default' => [
				'drivers' => [
					'Aeris\ZfAuth' => 'annotation_driver',
					'Aeris\ZfAuthTest' => 'annotation_driver',
				]
			]
		],
	]
];