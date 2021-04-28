<?php declare(strict_types = 1);

namespace Wedo\Api\Exceptions;

use Throwable;

class UnauthorizedException extends ResponseException
{

	public function __construct(string $message = '', int $code = 403, ?Throwable $previous = null)
	{
		if ($message === '') {
			$message = 'Operation not permitted!';
		}

		parent::__construct($message, $code, $previous);
	}

}
