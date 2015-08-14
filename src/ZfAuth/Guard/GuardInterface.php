<?php

namespace Aeris\ZfAuth\Guard;


use Zend\Mvc\Router\RouteMatch;

interface GuardInterface {

	public function __construct(array $rules = []);

	public function setRules(array $rules);


	/** @return boolean */
	public function isGranted(RouteMatch $event);

}