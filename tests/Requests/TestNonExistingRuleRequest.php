<?php declare(strict_types = 1);

namespace Wedo\Api\Tests\Requests;

use Wedo\Api\Requests\BaseRequest;

class TestNonExistingRuleRequest extends BaseRequest
{

	/**
	 * @control Text
	 * @addBlaSomething()
	 */
	public string $name;

}
