<?php


namespace Aeris\ZfAuth\Entity;


interface ResourceLimitInterface {

	/** @return string */
	public function getKey();

	/** @return int */
	public function getValue();

}