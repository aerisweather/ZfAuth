<?php

namespace Aeris\ZfAuth\Storage;


use Aeris\ZfAuth\Identity\AnonymousIdentity;
use Zend\Authentication\Storage\NonPersistent;

class AnonymousIdentityStorage extends NonPersistent {

	public function read() {
		if ($this->isEmpty()) {
			$this->write(new AnonymousIdentity());
		}

		return parent::read();
	}

}