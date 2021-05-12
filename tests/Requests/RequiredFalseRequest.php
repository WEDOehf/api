<?php declare (strict_types = 1);

namespace Wedo\Api\Tests\Requests;

use Wedo\Api\Attributes\Control;
use Wedo\Api\Attributes\RequiredRule;
use Wedo\Api\Requests\BaseRequest;

class RequiredFalseRequest extends BaseRequest
{

	#[Control(Control::TEXT)]
	#[RequiredRule(false)]
	public string $name;

}
