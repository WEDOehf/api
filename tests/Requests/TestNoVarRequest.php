<?php declare(strict_types = 1);

namespace Wedo\Api\Tests\Requests;

use Wedo\Api\Requests\BaseRequest;

//phpcs:disable
class TestNoVarRequest extends BaseRequest
{

	/**
	 * typehint not set here and this should throw an exception
	 */
	public $name;

}
