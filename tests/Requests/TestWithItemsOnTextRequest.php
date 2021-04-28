<?php declare(strict_types = 1);

namespace Wedo\Api\Tests\Requests;

use Wedo\Api\Requests\BaseRequest;

class TestWithItemsOnTextRequest extends BaseRequest
{

	/**
	 * @control Text
	 * @items {"1":1, "2":2, "3":3, "4":4, "5":5}
	 */
	public int $rating;

}
