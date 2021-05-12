<?php declare (strict_types = 1);

namespace Wedo\Api\Tests\Requests;

use Wedo\Api\Attributes\Control;
use Wedo\Api\Requests\BaseRequest;

class SimpleRequest2 extends BaseRequest
{

	#[Control(Control::TEXT)]
	public string $name;

}
