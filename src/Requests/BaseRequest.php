<?php declare (strict_types = 1);

namespace Wedo\Api\Requests;

use Nette\ArgumentOutOfRangeException;
use Nette\Forms\Container;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Controls\ChoiceControl;
use Nette\Forms\Form;
use Nette\NotSupportedException;
use Nette\Reflection\AnnotationsParser;
use Nette\Utils\ArrayHash;
use Nette\Utils\Strings;
use ReflectionNamedType;
use ReflectionObject;
use ReflectionProperty;
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

		$properties = $request->getAnnotations() ?? [];
		$this->formBuilder->createForm($properties, $request, $form, $data);

		$request->setValues($data);
	}


	/**
	 * Read annotations from public properties in current class
	 *
	 * @return array<int|string, string[]>|null
	 * @internal
	 */
	public function getAnnotations(): ?array
	{
		$reflectionProperties = $this->getReflectionProperties();
		$annotations = [];

		foreach ($reflectionProperties as $rp) {
			$annotations[$rp->name] = AnnotationsParser::getAll($rp);
		}

		return $annotations ?? null;
	}


	/**
	 * @internal
	 * @param string[][] $annotations
	 */
	public function setValidationRules(array $annotations, BaseControl $control): void
	{
		foreach ($annotations as $annotation => $args) {
			$this->setValidationRule($annotation, $control, $args);
		}
	}

	/**
	 * @param string[] $args
	 */
	public function setValidationRule(string $annotation, BaseControl $control, array $args): void
	{
		switch ($annotation) {
			case 'addCustomRule':
				$this->addCustomRule($control, $args);

				return;
			case 'addRule':
				$this->addRule($control, $args);

				return;
			case 'setRequired':
				$this->addRequiredRule($control, $args);

				return;
			case 'items':
				if (!$control instanceof ChoiceControl) {
					throw new NotSupportedException('Items annotation cannot be set on on control type "'
						. get_class($control) . '" in ' . static::class);
				}

				$this->setItems($control, $args);

				return;
			default:
				$callable = [$control->getRules(), $annotation];

				if (!method_exists($control->getRules(), $annotation) || !is_callable($callable)) {
					throw new NotSupportedException('Cannot apply rule "' . $annotation . '"!');
				}

				call_user_func_array($callable, $args);
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


	/**
	 * @param mixed[] $args
	 */
	private function addRule(BaseControl $control, array $args): void
	{
		foreach ($args as $arg) {
			if ($arg instanceof ArrayHash) {
				$addRuleArgs = (array) $arg;
				array_unshift($addRuleArgs, constant(Form::class . '::' . strtoupper(array_shift($addRuleArgs))));
			} else {
				$addRuleArgs = [constant(Form::class . '::' . strtoupper($arg))];
			}

			call_user_func_array([$control, 'addRule'], $addRuleArgs);
		}
	}


	/**
	 * @param mixed[] $args
	 */
	private function addCustomRule(BaseControl $control, array $args): void
	{
		foreach ($args as $arg) {
			$addRuleArgs = $arg instanceof ArrayHash ? (array) $arg : $arg;
			call_user_func_array([$control, 'addRule'], $addRuleArgs);
		}
	}


	/**
	 * @param mixed[] $args
	 */
	private function addRequiredRule(BaseControl $control, array $args): void
	{
		call_user_func_array([$control->getRules(), 'setRequired'], $args);
	}


	public function getForm(): Container
	{
		return $this->form;
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


	/**
	 * @param mixed[] $args
	 * @throws NotSupportedException
	 */
	private function setItems(ChoiceControl $control, array $args): void
	{
		if (Strings::startsWith($args[0], '[') || Strings::startsWith($args[0], '{')) {
			$items = json_decode($args[0], true);
			$control->setItems($items);
		} else {
			throw new NotSupportedException('Only Json is allowed for setting items on select in request!');
		}
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

}
