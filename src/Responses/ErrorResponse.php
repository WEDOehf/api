<?php declare(strict_types = 1);

namespace Wedo\Api\Responses;

use Wedo\Api\Exceptions\ResponseException;

class ErrorResponse extends BaseResponse
{

	public bool $success = false;

	public string $error;

	public function __construct(ResponseException $exception)
	{
		$this->error = $exception->getMessage();
	}

}
