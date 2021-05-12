<?php declare(strict_types = 1);

namespace Wedo\Api\Tests\Requests;

use Nette\Forms\Form;
use Wedo\Api\Attributes\Control;
use Wedo\Api\Attributes\ValidationRule;
use Wedo\Api\Requests\BaseRequest;

class TestRequest extends BaseRequest
{

	/**
	 * Customer name
	 */
	#[Control(Control::TEXT)]
	#[ValidationRule(Form::MAX_LENGTH, null, 20)]
	#[ValidationRule(Form::REQUIRED)]
	public string $name;

	/**
	 * Customers E-mail
	 */
	#[Control(Control::EMAIL)]
	#[ValidationRule(Form::MIN_LENGTH, null, 5)]
	public string $email;

	/**
	 * Customers phone
	 */
	#[Control(Control::TEXT)]
	#[ValidationRule([CustomValidator::class, 'validate'])]
	public string $phone;

	/**
	 * Customers birthYear
	 */
	#[Control(Control::TEXT)]
	#[ValidationRule(Form::NUMERIC)]
	public ?int $birth_year;

	/**
	 * Amount
	 */
	#[Control(Control::TEXT)]
	public float $amount;

	/**
	 * Accept tos
	 */
	#[Control(Control::CHECKBOX)]
	public bool $accept_tos;

}
