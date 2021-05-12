<?php declare(strict_types = 1);

namespace Wedo\Api\Tests\Requests;

use Wedo\Api\Attributes\ChoiceControlItems;
use Wedo\Api\Attributes\Control;
use Wedo\Api\Attributes\RequiredRule;
use Wedo\Api\Requests\BaseRequest;

class TestItemsRequest extends BaseRequest
{

	#[Control(Control::SELECT)]
	#[ChoiceControlItems([1 => 1, 2, 3, 4, 5])]
	#[RequiredRule()]
	public int $rating;

}
