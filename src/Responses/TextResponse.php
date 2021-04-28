<?php declare (strict_types = 1);

namespace Wedo\Api\Responses;

class TextResponse extends BaseResponse
{

	public string $data;

	public function __construct(string $data)
	{
		$this->data = $data;
	}

}
