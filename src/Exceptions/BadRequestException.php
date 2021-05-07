<?php declare (strict_types = 1);

namespace Wedo\Api\Exceptions;

use Throwable;

class BadRequestException extends ResponseException
{

	/**
	 * @codeCoverageIgnore
	 */
	public function __construct(string $message = '', int $code = 400, ?Throwable $previous = null, mixed ...$parameters)
	{
		parent::__construct($message, $code, $previous, $parameters);
	}

}
