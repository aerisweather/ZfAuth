<?php


namespace Aeris\ZfAuth\Service;


interface EncryptorInterface {

	/**
	 * @param string $str
	 */
	public function encrypt($str);

	/**
	 * Verify if a string matches a hashed value
	 *
	 * @param $str
	 * @param $hash
	 * @return boolean
	 */
	public function verify($str, $hash);
} 