<?php declare(strict_types = 1);

namespace Wedo\Api\Tests\Requests;

use Wedo\Api\Requests\BaseRequest;

//phpcs:disable
class TestUnsupportedControlTypeRequest extends BaseRequest
{

	/**
	 * @control nonExistingType123
	 */
	public $name;

}
