<?php declare (strict_types = 1);

namespace Wedo\Api\Exceptions;

use Throwable;

class NotFoundException extends ResponseException
{

	public function __construct(string $message = '', int $code = 404, ?Throwable $previous = null)
	{
		if ($message === '') {
			$message = 'Action does not exist!';
		}

		parent::__construct($message, $code, $previous);
	}

}
