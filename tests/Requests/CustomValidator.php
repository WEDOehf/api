<?php declare (strict_types = 1);

namespace Wedo\Api\Tests\Requests;

use Nette\Forms\Controls\TextInput;

class CustomValidator
{

	public static function validate(TextInput $kennitala): bool
	{
		return true;
	}

}
