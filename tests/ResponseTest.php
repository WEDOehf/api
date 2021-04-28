<?php declare (strict_types = 1);

namespace Wedo\Api\Tests;

use PHPUnit\Framework\TestCase;
use Wedo\Api\Exceptions\ResponseException;
use Wedo\Api\Responses\ErrorResponse;
use Wedo\Api\Responses\TextResponse;

class ResponseTest extends TestCase
{

	public function testErrorResponse(): void
	{
		$response = new ErrorResponse(new ResponseException('test', 502));
		$this->assertFalse($response->success);
		$this->assertEquals('test', $response->error);
	}

	public function testTextResponse(): void
	{
		$response = new TextResponse('test');
		$this->assertTrue($response->success);
		$this->assertEquals('test', $response->data);
	}

}
