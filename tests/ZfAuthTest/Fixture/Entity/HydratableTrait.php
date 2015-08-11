<?php
/**
 * Created by PhpStorm.
 * User: edanschwartz
 * Date: 8/10/15
 * Time: 5:02 PM
 */

namespace Aeris\ZfAuthTest\Fixture\Entity;


trait HydratableTrait {

	public function hydrate(array $props) {
		foreach ($props as $key => $val) {
			if (property_exists($this, $key)) {
				$this->$key = $val;
			}
		}
	}

}