<?php declare(strict_types = 1);

namespace Wedo\Api\Entities;

class ValidationError
{

	public string $field;

	/** @var string[] */
	public array $errors;

	/**
	 * @param string[] $errors
	 */
	public function __construct(string $field, array $errors)
	{
		$this->field = $field;
		$this->errors = $errors;
	}

}
