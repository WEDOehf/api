<?php declare(strict_types = 1);

namespace Wedo\Api\Tests\Requests;

use Nette\Forms\Form;
use Wedo\Api\Attributes\ContainerType;
use Wedo\Api\Attributes\Control;
use Wedo\Api\Attributes\ValidationRule;
use Wedo\Api\Requests\BaseRequest;

class TestArrayRequest extends BaseRequest
{

	/** @var SimpleRequest[] $items */
	#[Control(Control::CONTAINER)]
	#[ValidationRule(Form::REQUIRED)]
	#[ContainerType(SimpleRequest::class)]
	public array $items;

	/** @var SimpleRequest2[] $item2 */
	#[Control(Control::CONTAINER)]
	#[ContainerType(SimpleRequest2::class)]
	public array $item2;

}
