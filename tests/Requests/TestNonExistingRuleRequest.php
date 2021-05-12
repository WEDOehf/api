<?php declare(strict_types = 1);

namespace Wedo\Api\Tests\Requests;

use Wedo\Api\Attributes\Control;
use Wedo\Api\Attributes\ValidationRule;
use Wedo\Api\Requests\BaseRequest;

class TestNonExistingRuleRequest extends BaseRequest
{

	#[Control(Control::TEXT)]
	#[ValidationRule('BlaSomething')]
	public string $name;

}
