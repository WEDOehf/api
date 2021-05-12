<?php declare (strict_types = 1);

namespace Wedo\Api\Attributes;

use Attribute;

#[Attribute]
class RequiredRule
{

	public bool $required;

	public function __construct(bool $required = true)
	{
		$this->required = $required;
	}

}
