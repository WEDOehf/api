<?php declare(strict_types = 1);

namespace Wedo\Api\Tests\Requests;

use Wedo\Api\Attributes\Control;
use Wedo\Api\Requests\BaseRequest;

//phpcs:disable
class TestNotBuiltInTypeRequest extends BaseRequest
{

	/**
	 * typehint not set here and this should throw an exception
	 */
	#[Control(Control::TEXT)]
	public \stdClass $name;

}
