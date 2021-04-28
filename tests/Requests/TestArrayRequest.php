<?php declare(strict_types = 1);

namespace Wedo\Api\Tests\Requests;

use Wedo\Api\Requests\BaseRequest;

class TestArrayRequest extends BaseRequest
{

	/**
	 * @var SimpleRequest[]
	 * @control Container
	 * @setRequired()
	 */
	public array $items;

	/**
	 * @var SimpleRequest2[]
	 * @control Container
	 */
	public array $item2;

}
