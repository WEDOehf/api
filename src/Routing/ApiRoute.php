<?php declare(strict_types = 1);

namespace Wedo\Api\Routing;

use Nette;
use Nette\Application\Routers\Route;
use Nette\Http\IRequest;
use Nette\Routing\Router;
use Nette\Utils\Strings;

class ApiRoute implements Router
{

	private string $prefix;

	private string $controllerNamespace;

	/** @var string[] */
	private array $apiEndPoints;

	/**
	 * @param string[] $apiEndpoints
	 */
	public function __construct(string $prefix, string $controllerNamespace, array $apiEndpoints)
	{
		$this->prefix = $prefix;
		$this->controllerNamespace = $controllerNamespace;
		$this->apiEndPoints = $apiEndpoints;
	}

	/**
	 * @return mixed[]|null
	 */
	public function match(IRequest $context): ?array
	{
		$path = $context->getUrl()->getPath();

		if (!Strings::startsWith($path, $this->prefix)) {
			return null;
		}

		$url = trim(substr($path, strlen($this->prefix)), '/');

		$presenterAndAction = $this->getPresenterAndAction($url);

		if ($presenterAndAction === null) {
			return null;
		}

		/**
		 * @var string $presenter
		 * @var string $action
		 * @var string[] $params
		 */
		[$presenter, $action, $params] = $presenterAndAction;
		$presenter = $this->controllerNamespace . ':' . $presenter;
		$params += $context->getQuery();
		$params['presenter'] = $presenter;
		$params['action'] = Route::path2action($action);

		return $params;
	}

	/**
	 * Constructs absolute URL from Request object. Not implemented for API, since its not needed
	 *
	 * @param mixed[] $appRequest
	 */
	public function constructUrl(array $appRequest, Nette\Http\UrlScript $refUrl): ?string
	{
		return null;
	}

	/**
	 * @param mixed[] $paramsPart
	 * @return  array<int, array|string|false> [$presenter, $action, mixed]
	 */
	private function getPresenterAndAction(string $url, array $paramsPart = []): ?array
	{
		$urlParts = explode('/', $url);

		if (isset($this->apiEndPoints[$url])) {
			return [$this->apiEndPoints[$url], end($urlParts), $paramsPart];
		}

		array_unshift($paramsPart, end($urlParts));

		array_pop($urlParts);

		if (count($urlParts) < 2) {
			return null;
		}

		$url = implode('/', $urlParts);

		return $this->getPresenterAndAction($url, $paramsPart);
	}

}
