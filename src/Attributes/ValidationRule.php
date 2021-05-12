<?php declare (strict_types = 1);

namespace Wedo\Api\Attributes;

use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY)]
class ValidationRule
{

	/** @var callable|string */
	public $validator;

	public string|object|null $errorMessage;

	public mixed $args;

	public function __construct(callable|string $validator, string|object|null $errorMessage = null, mixed $args = null)
	{
		$this->validator = $validator;
		$this->errorMessage = $errorMessage;
		$this->args = $args;
	}

}
