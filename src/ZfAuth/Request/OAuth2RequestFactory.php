<?php


namespace Aeris\ZfAuth\Request;

use Zend\Http\Request as HttpRequest;
use OAuth2\Request as OAuth2Request;

class OAuth2RequestFactory {

	public static function create(HttpRequest $request) {
		$queryParams = $request->getQuery()->toArray();
		$postParams  = $request->getPost()->toArray();
		$files       = $request->getFiles()->toArray();
		$cookies     = ($c = $request->getCookie()) ? [$c] : [];

		return new OAuth2Request($queryParams, $postParams, [], $cookies, $files, $_SERVER);
	}

}