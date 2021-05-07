<?php declare(strict_types = 1);

namespace Wedo\Api\Tests\Routing;

use Nette\Http\Request;
use Nette\Http\UrlScript;
use PHPUnit\Framework\TestCase;
use Wedo\Api\Routing\ApiRoute;
use Wedo\Api\Routing\RouterFactory;

class RoutingTest extends TestCase
{

	public function testRouterFullCircle(): void
	{
		$factory = new RouterFactory(['article/get' => 'Article'], 'Api', '/api/v1');
		$route = $factory->create();

		$request = $route->match($this->createRequest('/api/v1/article/get'));
		$this->assertEquals(['presenter' => 'Api:Article', 'action' => 'get'], $request);
	}

	public function testMatch_WithActionInRootNamespace(): void
	{
		$route = new ApiRoute('/api/v1', 'Api', ['checkout/get' => 'Checkout']);

		$request = $route->match($this->createRequest('/api/v1/checkout/get'));
		$this->assertEquals(['presenter' => 'Api:Checkout', 'action' => 'get'], $request);
	}

	public function testMatch_WithParameterInUrl(): void
	{
		$route = new ApiRoute('/api/v1', 'Api', ['product/get' => 'Product']);

		$request = $route->match($this->createRequest('/api/v1/product/get/product-slug'));
		$this->assertEquals($request, ['presenter' => 'Api:Product', 'action' => 'get', 'product-slug']);
	}

	public function testMatch_WithNotExistingService_ShoulgReturnNull(): void
	{
		$route = new ApiRoute('/api/v1', 'Api', []);
		$this->assertNull($route->match($this->createRequest('/api/v1/route1/not-exist')));
	}

	public function testMatchNotApiUrl(): void
	{
		$route = new ApiRoute('/api/v1', 'Api', []);
		$this->assertNull($route->match($this->createRequest('/api/v')));
	}

	public function testConstruct(): void
	{
		$route = new ApiRoute('/api/v1', 'Api', []);
		$this->assertNull($route->constructUrl(['presenter' => 'Api:Default'], new UrlScript('http://domain.local')));
	}

	private function createRequest(string $path): Request
	{
		$url = new UrlScript('http://domain.local' . $path);

		return new Request($url);
	}

}
