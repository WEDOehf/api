<?php declare (strict_types = 1);

namespace Wedo\Api\Attributes;

use Attribute;

#[Attribute]
class ContainerType
{

	/** @var class-string */
	public string $value;

	/**
	 * @param class-string $value
	 */
	public function __construct(string $value)
	{
		$this->value = $value;
	}

}
