<?php declare (strict_types = 1);

namespace Wedo\Api\Tests\Requests;

use Wedo\Api\Requests\BaseRequest;

class SimpleRequest extends BaseRequest
{

	/**
	 * @setRequired
	 * @control Text
	 */
	public string $name;

}
