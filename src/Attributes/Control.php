<?php declare (strict_types = 1);

namespace Wedo\Api\Attributes;

use Attribute;

#[Attribute]
class Control
{

	public const TEXT = 'Text';
	public const PASSWORD = 'Password';
	public const TEXT_AREA = 'TextArea';
	public const EMAIL = 'Email';
	public const INTEGER = 'Integer';
	public const UPLOAD = 'Upload';
	public const MULTI_UPLOAD = 'MultiUpload';
	public const HIDDEN = 'Hidden';
	public const CHECKBOX = 'Checkbox';
	public const RADIO_LIST = 'RadioList';
	public const CHECKBOX_LIST = 'CheckboxList';
	public const SELECT = 'Select';
	public const MULTI_SELECT = 'MultiSelect';
	public const IMAGE = 'Image';
	public const CONTAINER = 'Container';

	public string $value;

	public function __construct(string $value)
	{
		$this->value = $value;
	}

}
