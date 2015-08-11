<?php


namespace Aeris\ZfAuthTest\Fixture\Entity;


class Animal {
	use HydratableTrait;


	public $type;

	public function __construct(array $props = []) {
		$this->hydrate($props);
	}
}