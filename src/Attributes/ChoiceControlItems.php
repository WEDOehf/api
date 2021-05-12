<?php declare (strict_types = 1);

namespace Wedo\Api\Attributes;

use Attribute;

#[Attribute]
class ChoiceControlItems
{

	/** @var mixed[] */
	public array $items;

	public bool $useKeys;

	/**
	 * @param mixed[] $items
	 */
	public function __construct(array $items, bool $useKeys = false)
	{
		$this->items = $items;
		$this->useKeys = $useKeys;
	}

}
