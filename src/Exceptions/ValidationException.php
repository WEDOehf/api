<?php declare (strict_types = 1);

namespace Wedo\Api\Exceptions;

use Wedo\Api\Entities\ValidationError;

class ValidationException extends ResponseException
{

	/** @var ValidationError[] */
	private array $validationErrors;

	/**
	 * @param ValidationError[] $validationErrors
	 */
	public function __construct(array $validationErrors)
	{
		parent::__construct('Data is not Valid!', 422);

		$this->validationErrors = $validationErrors;
	}

	/**
	 * @return ValidationError[]
	 * @codeCoverageIgnore
	 */
	public function getValidationErrors(): array
	{
		return $this->validationErrors;
	}

	/**
	 * @param ValidationError[] $validationErrors
	 * @codeCoverageIgnore
	 */
	public function setValidationErrors(array $validationErrors): void
	{
		$this->validationErrors = $validationErrors;
	}

}
