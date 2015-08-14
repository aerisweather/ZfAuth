<?php


namespace Aeris\ZfAuthTest\Fixture\Controller;


use Zend\Mvc\Controller\AbstractRestfulController;

class AdminController extends AbstractRestfulController {

	public function delete($id) {
		return [];
	}

	public function fooAction() {
		return [];
	}

}