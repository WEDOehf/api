<?php declare (strict_types = 1);

namespace Wedo\Api\DI;

use Wedo\Api\Controllers\Controller;

class ApiExtensionConfig
{

	/** @var class-string */
	public string $controller = Controller::class;

	public string $url = '/api/v1/';

	/**
	 * controller namespace default is Api (this is first part of controller name in routing, like Api:Article)
	 */
	public string $controllerNameNamespace = 'Api';

}
