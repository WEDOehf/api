<?php declare(strict_types = 1);

namespace Wedo\Api\Tests\Requests;

use Wedo\Api\Requests\BaseRequest;

class TestItemsRequest extends BaseRequest
{

	/**
	 * @control Select
	 * @items {"1":1, "2":2, "3":3, "4":4, "5":5}
	 * @setRequired
	 */
	public int $rating;

}
