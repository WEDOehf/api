<?php declare(strict_types = 1);

namespace Wedo\Api\Tests\Requests;

use Wedo\Api\Requests\BaseRequest;

class TestItemsNotJsonRequest extends BaseRequest
{

	/**
	 * @control Select
	 * @items 1,2,3,4
	 */
	public int $rating;

}
