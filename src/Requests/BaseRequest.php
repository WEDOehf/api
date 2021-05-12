<?php declare (strict_types = 1);

namespace Wedo\Api\Requests;

use Nette\ArgumentOutOfRangeException;
use Nette\Forms\Container;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Form;
use Nette\NotSupportedException;
use Nette\Utils\ArrayHash;
use ReflectionNamedType;
use ReflectionObject;
use ReflectionProperty;
use Wedo\Api\Attributes\RequiredRule;
use Wedo\Api\Attributes\ValidationRule;
use Wedo\Api\Entities\ValidationError;
use Wedo\Api\Exceptions\ValidationException;
use Wedo\Api\Helpers\FormBuilder;
use Wedo\Api\Helpers\RequestHydrator;

/**
 * For more info on validation
 *
 * @see https://doc.nette.org/en/3.0/forms#toc-validation-rules
 */
abstract class BaseRequest
{

	/**
	 * Underlying form
	 */
	private Container $form;

	private FormBuilder $formBuilder;

	/** @var ReflectionProperty[] */
	private array $reflectionProperties;

	public function __construct(?FormBuilder $formBuilder = null)
	{
		$this->formBuilder = $formBuilder ?? new FormBuilder();
	}

	/**
	 * @param mixed[] $data
	 * @param Container $form ($this->form by default)
	 * @throws ArgumentOutOfRangeException
	 * @throws NotSupportedException
	 */
	public function buildForm(array $data = [], ?Container $form = null, ?BaseRequest $request = null): void
	{
		$request ??= $this;
		$form = $request->setForm($form);

		$properties = $request->getReflectionProperties();
		$this->formBuilder->createForm($properties, $request, $form, $data);

		$request->setValues($data);
	}

	/**
	 * @internal
	 */
	public function setValidationRules(ReflectionProperty $property, BaseControl $control): void
	{
		$validationRulesAttributes = $property->getAttributes(ValidationRule::class);

		foreach ($validationRulesAttributes as $validationRuleAttribute) {
			/** @var ValidationRule $validationRule */
			$validationRule = $validationRuleAttribute->newInstance();
			$control->addRule($validationRule->validator, $validationRule->errorMessage, $validationRule->args);
		}

		$requiredRuleAttributes = $property->getAttributes(RequiredRule::class);

		if (count($requiredRuleAttributes) > 0) {
			/** @var RequiredRule $requiredRule */
			$requiredRule = $requiredRuleAttributes[0]->newInstance();
			$control->setRequired($requiredRule->required);
		}
	}

	/**
	 * @param mixed[] $values
	 */
	public function setValues(array $values): void
	{
		$this->form->setValues($this->valuesToString($values));
		$this->fillFromForm();
	}

	/**
	 * @return array<string, mixed>
	 */
	public function toArray(): array
	{
		$arr = [];

		foreach ($this->getReflectionProperties() as $property) {
			if (!isset($this->{$property->getName()})) {
				continue;
			}

			if (is_array($this->{$property->getName()})) {
				foreach ($this->{$property->getName()} as $key => $subRequest) {
					$arr[$property->getName()][$key] = $subRequest->toArray();
				}

				continue;
			}

			$arr[$property->getName()] = $this->{$property->getName()};

		}

		return $arr;
	}

	/**
	 * @param mixed[] $values
	 * @return string[][]
	 */
	public function valuesToString(array $values): array
	{
		foreach ($values as $key => $value) {
			if (is_scalar($value)) {
				$values[$key] = (string) $value;
			}

			if (is_array($value)) {
				$values[$key] = $this->valuesToString($value);
			}
		}

		return $values;
	}

	/**
	 * @internal
	 */
	public function fillFromForm(): void
	{
		/** @var ArrayHash $values */
		$values = $this->form->getUnsafeValues(null);

		$properties = $this->getReflectionProperties();

		foreach ($values as $key => $value) {
			if ($value instanceof ArrayHash) {
				// this is already set in recursive calls and if we set it up we will destroy request objects created in recursive call
				continue;
			}

			/** @var ReflectionNamedType $refType */
			$refType = $properties[$key]->getType();
			$this->$key = RequestHydrator::hydrateValue($refType, $value);
		}
	}

	/**
	 * @internal
	 */
	public function isValid(): bool
	{
		return $this->form->isValid();
	}

	/**
	 * @throws ValidationException
	 */
	public function validate(): void
	{
		if ($this->isValid()) {
			return;
		}

		$errors = [];

		foreach ($this->getErrors() as $field => $error) {
			$errors[] = new ValidationError($field, $error);
		}

		throw new ValidationException($errors);
	}

	/**
	 * Returns array in control => error way
	 *
	 * @return array<string, string[]>
	 */
	public function getErrors(): array
	{
		$errors = [];

		/** @var BaseControl $control */
		foreach ($this->form->controls as $control) {
			$controlError = $control->getErrors();

			if (count($controlError) === 0) {
				continue;
			}

			$arr = [$control->getName() => $controlError];
			$parentControl = $control->getParent();

			if ($parentControl instanceof Form) {
				$errors[$control->getName()] = $controlError;

				continue;
			}

			while (!$parentControl instanceof Form && $parentControl instanceof Container) {
				$newArr = [];
				$newArr[$parentControl->getName()] = $arr;

				if ($parentControl->getParent() instanceof Form) {
					$errors[$parentControl->getName()] = $arr;
				}

				$parentControl = $parentControl->getParent();
				$arr = $newArr;
			}
		}

		return $errors;
	}

	public function getForm(): Container
	{
		return $this->form;
	}

	/**
	 * @return ReflectionProperty[]
	 */
	public function getReflectionProperties(): array
	{
		if (!isset($this->reflectionProperties)) {
			$properties = (new ReflectionObject($this))->getProperties(ReflectionProperty::IS_PUBLIC);
			$this->reflectionProperties = [];

			foreach ($properties as $property) {
				$this->reflectionProperties[$property->getName()] = $property;
			}
		}

		return $this->reflectionProperties;
	}

	protected function setForm(?Container $form = null): Container
	{
		if ($form === null) {
			$form = $this->formBuilder->createEmptyForm();
		}

		$this->form = $form;

		if ($form instanceof Form) {
			$form->onSubmit[] = function (): void {
				$this->fillFromForm();
			};
		}

		return $form;
	}

}
