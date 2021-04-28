<?php declare (strict_types = 1);

namespace Wedo\Api\Tests\Requests;

use Wedo\Api\Requests\BaseRequest;

class RequiredFalseRequest extends BaseRequest
{

	/**
	 * @setRequired(FALSE)
	 * @control Text
	 */
	public string $name;

}
