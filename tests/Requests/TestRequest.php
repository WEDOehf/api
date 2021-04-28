<?php declare(strict_types = 1);

namespace Wedo\Api\Tests\Requests;

use Wedo\Api\Requests\BaseRequest;

class TestRequest extends BaseRequest
{

	/**
	 * Customer name
	 *
	 * @control Text
	 * @addRule(max_length, NULL, 20)
	 * @setRequired
	 */
	public string $name;

	/**
	 * Customers E-mail
	 *
	 * @control Email
	 * @addRule(min_length, NULL, 5)
	 */
	public string $email;

	/**
	 * Customers phone
	 *
	 * @addCustomRule('Wedo\Api\Tests\Requests\CustomValidator::validate', 'phone not valid')
	 * @control Text
	 */
	public string $phone;

	/**
	 * Customers birthYear
	 *
	 * @control Text
	 * @addRule(numeric)
	 */
	public ?int $birth_year;

	/**
	 * Customers birthYear
	 *
	 * @control Text
	 * @isRequired()
	 */
	public float $amount;

	/**
	 * Accept tos
	 *
	 * @control CheckBox
	 */
	public bool $accept_tos;

}
