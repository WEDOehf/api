<?php declare (strict_types = 1);

namespace Wedo\Api\Helpers;

use Nette\Application\UI\Form;
use Nette\ArgumentOutOfRangeException;
use Nette\Forms\Container;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Controls\ChoiceControl;
use Nette\NotSupportedException;
use ReflectionProperty;
use Throwable;
use Wedo\Api\Attributes\ChoiceControlItems;
use Wedo\Api\Attributes\ContainerType;
use Wedo\Api\Attributes\Control;
use Wedo\Api\Requests\BaseRequest;

class FormBuilder
{

	/**
	 * @param ReflectionProperty[] $properties
	 * @param mixed[] $data
	 */
	public function createForm(array $properties, BaseRequest $request, Container $form, array $data): void
	{
		foreach ($properties as $property) {
			$controlType = $this->getControlType($property);
			$controlAddCallback = [$form, 'add' . $controlType];

			try {
				$control = $controlAddCallback($property->getName()); //@phpstan-ignore-line
			} catch (Throwable $ex) {
				throw new NotSupportedException('Cannot add control of type ' . $controlType, 0, $ex);
			}

			if ($controlType === Control::CONTAINER) {
				/** @phpstan-ignore-next-line */
				$request->{$property->getName()} = [];

				/** @phpstan-ignore-next-line */
				if (empty($data[$property->getName()])) {
					continue;
				}

				$values = $data[$property->getName()];

				$requestTypeAttributes = $property->getAttributes(ContainerType::class);

				if (count($requestTypeAttributes) === 0) {
					throw new NotSupportedException('ContainerType attribute not found for ' .
						$property->getDeclaringClass()->getName() . '::' . $property->getName());
				}

				/** @var ContainerType $requestTypeAttribute */
				$requestTypeAttribute = $requestTypeAttributes[0]->newInstance();

				$requestType = $requestTypeAttribute->value;

				foreach ($values as $key => $value) {
					/** @var Container $container */
					$container = $control->addContainer($key);
					$item = new $requestType();
					$item->buildForm($value, $container, $item);
					/** @phpstan-ignore-next-line */
					$request->{$property->getName()}[] = $item;
				}
			}

			if ($control instanceof BaseControl) {
				$request->setValidationRules($property, $control);
			}

			if ($control instanceof ChoiceControl) {
				$itemsAttributes = $property->getAttributes(ChoiceControlItems::class);

				if (count($itemsAttributes) > 0) {
					/** @var ChoiceControlItems $itemAttribute */
					$itemAttribute = $itemsAttributes[0]->newInstance();
					$control->setItems($itemAttribute->items, $itemAttribute->useKeys);
				}
			}
		}
	}

	public function createEmptyForm(): Form
	{
		return new Form();
	}

	/**
	 * @throws ArgumentOutOfRangeException
	 */
	protected function getControlType(ReflectionProperty $property): string
	{
		$controlAttributes = $property->getAttributes(Control::class);

		if (count($controlAttributes) === 0) {
			throw new ArgumentOutOfRangeException('#[Control] Attribute not set on ' .
				$property->getDeclaringClass()->getName() . '::' . $property->getName());
		}

		/** @var Control $controlAttribute */
		$controlAttribute = $controlAttributes[0]->newInstance();

		return $controlAttribute->value;
	}

}
