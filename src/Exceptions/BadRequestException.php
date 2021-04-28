<?php declare (strict_types = 1);

namespace Wedo\Api\Exceptions;

use Throwable;

class BadRequestException extends ResponseException
{

	/**
	 * @param mixed|string ...$parameters
	 * @codeCoverageIgnore
	 */
	public function __construct(string $message = '', int $code = 400, ?Throwable $previous = null, ...$parameters)
	{
		parent::__construct($message, $code, $previous, $parameters);
	}

}
