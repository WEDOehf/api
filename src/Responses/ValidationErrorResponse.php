<?php declare(strict_types = 1);

namespace Wedo\Api\Responses;

use Wedo\Api\Entities\ValidationError;
use Wedo\Api\Exceptions\ValidationException;

class ValidationErrorResponse extends ErrorResponse
{

	public string $error = 'Data not valid!';

	/** @var ValidationError[] */
	public array $validation_errors;

	/**
	 * @codeCoverageIgnore
	 */
	public function __construct(ValidationException $exception)
	{
		parent::__construct($exception);
		$this->validation_errors = $exception->getValidationErrors();
	}

}
