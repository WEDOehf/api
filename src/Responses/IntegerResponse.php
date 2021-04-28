<?php declare(strict_types = 1);

namespace Wedo\Api\Responses;

class IntegerResponse extends BaseResponse
{

	/**
	 * id of saved item
	 */
	public int $data;

	/**
	 * @codeCoverageIgnore
	 */
	public function __construct(int $data)
	{
		$this->data = $data;
	}

}
