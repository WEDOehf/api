<?php declare (strict_types = 1);

namespace Wedo\Api\Attributes;

#[Attribute]
class HttpMethod
{

	public string $value;

	public function __construct(string $value)
	{
		$this->value = $value;
	}
}
