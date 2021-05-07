<?php declare (strict_types = 1);

namespace Wedo\Api\Attributes;

use Attribute;

#[Attribute]
class HttpMethod
{

	public string $value;

	public function __construct(string $value)
	{
		$this->value = $value;
	}

}
