<?php declare (strict_types = 1);

namespace Wedo\Api\Routing;

class RouterFactory
{

	/** @var string[] */
	private array $apiEndPoints;

	private string $url;

	private string $controllerNamespace;

	/**
	 * @param string[] $apiEndPoints
	 */
	public function __construct(array $apiEndPoints, string $controllerNamespace, string $url)
	{
		$this->apiEndPoints = $apiEndPoints;
		$this->controllerNamespace = $controllerNamespace;
		$this->url = $url;
	}

	public function create(): ApiRoute
	{
		return new ApiRoute($this->url, $this->controllerNamespace, $this->apiEndPoints);
	}

}
