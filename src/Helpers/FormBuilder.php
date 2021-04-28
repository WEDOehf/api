<?php declare (strict_types = 1);

namespace Wedo\Api\Helpers;

use Nette\Application\UI\Form;
use Nette\ArgumentOutOfRangeException;
use Nette\Forms\Container;
use Nette\Forms\Controls\BaseControl;
use Nette\NotSupportedException;
use Wedo\Api\Requests\BaseRequest;
use Wedo\Utilities\ClassNameHelper;

class FormBuilder
{

	/**
	 * @param array<int|string, string[]> $properties
	 * @param mixed[] $data
	 */
	public function createForm(array $properties, BaseRequest $request, Container $form, array $data): void
	{
		/**
		 * @var string $property
		 * @var string[][] $annotations
		 */
		foreach ($properties as $property => $annotations) {
			$controlType = $this->getControlType($annotations);
			$controlAddCallback = [$form, 'add' . $controlType];

			if ((!method_exists($form, $controlAddCallback[1]) && $controlType !== 'Date') || !is_callable($controlAddCallback)) {
				throw new NotSupportedException('Control of type ' . $controlType . ' does not exist!');
			}

			$control = call_user_func($controlAddCallback, $property);

			if ($controlType === 'Container') {
				/** @phpstan-ignore-next-line */
				$request->$property = [];

				/** @phpstan-ignore-next-line */
				if (empty($data[$property])) {
					continue;
				}

				$values = $data[$property];

				$reqType = rtrim($annotations['var'][0], '][');
				$reqType = ClassNameHelper::extractFqnFromObjectUseStatements($request, $reqType);

				foreach ($values as $key => $value) {
					/** @var Container $container */
					$container = $control->addContainer($key);
					$item = new $reqType();
					$item->buildForm($value, $container, $item);
					/** @phpstan-ignore-next-line */
					$request->$property[] = $item;
				}
			}

			unset($annotations['var'], $annotations['description'], $annotations['control']);

			if ($control instanceof BaseControl) {
				$request->setValidationRules($annotations, $control);
			}
		}
	}

	/**
	 * @param string[][] $annotations
	 * @throws ArgumentOutOfRangeException
	 * @throws NotSupportedException
	 */
	protected function getControlType(array $annotations): string
	{
		if (!isset($annotations['control'])) {
			throw new ArgumentOutOfRangeException('@control annotation not set on request!');
		}

		return $annotations['control'][0];
	}

	public function createEmptyForm(): Form
	{
		return new Form();
	}

}
