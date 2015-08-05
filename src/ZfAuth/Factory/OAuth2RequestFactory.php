<?php

namespace Aeris\ZfAuth\Factory;


use Zend\Http\Request;
use Zend\Mvc\Application;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use OAuth2\Request as OAuth2Request;


class OAuth2RequestFactory implements FactoryInterface {

	/**
	 * Create service
	 *
	 * @param ServiceLocatorInterface $serviceLocator
	 * @return \OAuth2\Request
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		/** @var Application $application */
		$application = $serviceLocator->get('Application');
		/** @var Request $request */
		$request = $application
			->getMvcEvent()
			->getRequest();

		$queryParams = $request->getQuery()->toArray();
		$postParams  = $request->getPost()->toArray();
		$files       = $request->getFiles()->toArray();
		$cookies     = ($c = $request->getCookie()) ? [$c] : [];

		return new OAuth2Request($queryParams, $postParams, [], $cookies, $files, $_SERVER);
	}
}