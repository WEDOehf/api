<?php declare(strict_types = 1);

namespace Wedo\Api\Tests\Requests;

use Nette\Utils\Json;
use PHPUnit\Framework\TestCase;
use Wedo\Api\Exceptions\ValidationException;

class RequestTest extends TestCase
{

	public function testIsValid_WithCorrectInput_ShouldReturnTrue(): void
	{
		$request = new TestRequest();
		$request->buildForm([
			'name' => 'Dalibor Korpar',
			'phone' => '0915151',
			'email' => 'dalibor.korpar@gmail.com',
			'birth_year' => null,
		]);

		$this->assertTrue($request->isValid());
	}

	public function testIsValid_WithoutRequiredData_ShouldReturnFalse(): void
	{
		$request = new TestRequest();
		$request->buildForm([
			'phone' => '0915151',
			'email' => 'dalibor.korpar@gmail.com',
		]);
		$this->assertFalse($request->isValid());

		$errors = $request->getErrors();
		$this->assertNotEmpty($errors['name']);

		//values should never be omited
		$this->assertFalse($request->getForm()['name']->isOmitted());
	}

	public function testToArrayWithSkippedValue(): void
	{
		$request = new SimpleRequest();
		$arr = $request->toArray();
		$this->assertEmpty($arr);
	}

	public function testNoVarAnnotationSet(): void
	{
		$this->expectException('Exception');
		$request = new TestNoVarRequest();
		$request->buildForm();
	}

	public function testSetRequiredFalse(): void
	{
		$request = new RequiredFalseRequest();
		$request->buildForm([]);
		$request->validate();
		$this->assertTrue(true);
	}

	public function testNonExistingRule(): void
	{
		$this->expectExceptionMessage('Cannot apply rule "addBlaSomething"!');
		$request = new TestNonExistingRuleRequest();
		$request->buildForm();
	}

	public function testItemsOnTextControlType_ShouldThrowException(): void
	{
		$this->expectExceptionMessage('Items annotation cannot be set on on control type');
		$request = new TestWithItemsOnTextRequest();
		$request->buildForm();
	}

	public function testUnsupportedControlType(): void
	{
		$this->expectExceptionMessage('Control of type nonExistingType123 does not exist!');
		$request = new TestUnsupportedControlTypeRequest();
		$request->buildForm();
	}

	public function testNotBuiltInType(): void
	{
		$this->expectExceptionMessage('Only built in types are supported! stdClass is not supported');
		$request = new TestNotBuiltInTypeRequest();
		$request->buildForm();
	}

	public function testArrayRequest_WithValidData_ReturnsTrue(): void
	{
		$request = new TestArrayRequest();
		$request->buildForm(
			[
				'items' => [
					0 => [
							'name' => 'Mario',
						],
					1 => [
						'name' => 'Ingi',
					],
				],
			]
		);
		$this->assertTrue($request->isValid());
	}

	public function testArrayRequest_WithInvalidData_ReturnsFalse(): void
	{
		$request = new TestArrayRequest();
		$data = [
			'items' => [
				0 => [
					'name' => '123',
				],
				1 => [
					'name' => null,
				],
			]];
		$request->buildForm($data);
		$this->assertFalse($request->isValid());
		$errors = $request->getErrors();
		$this->assertArrayHasKey('items', $errors);
		$this->assertArrayHasKey('1', $errors['items']);
		$this->assertArrayHasKey('name', $errors['items'][1]);

		$this->assertEquals($data, $request->toArray());
	}

	public function testContainer_WithValidJson(): void
	{
		$json = '{"items":[{"name":"bla"}]}';
		$request = new TestArrayRequest();
		$inputData = Json::decode($json, Json::FORCE_ARRAY);
		$request->buildForm($inputData);
		$request->validate();
		$this->assertTrue(true);
	}

	public function testContainer_WithValidJsonAndMultipleItems(): void
	{
		$json = '{"items":[{"name":"54053"},{"name":"54052"}]}';
		$request = new TestArrayRequest();
		$inputData = Json::decode($json, Json::FORCE_ARRAY);
		$request->buildForm($inputData);
		$request->validate();
		$this->assertCount(2, $request->items);
	}

	public function testValidate_WithInvalidData_ShouldThrowException(): void
	{
		$this->expectException(ValidationException::class);
		$request = new TestRequest();
		$request->buildForm([
			'phone' => '0915151',
			'email' => 'dalibor.korpar@gmail.com',
		]);
		$request->validate();
	}

	public function testValidate_WithRequiredDataNotFilled(): void
	{
		$this->expectException(ValidationException::class);
		$req = new SimpleRequest();
		$req->buildForm();
		$req->validate();
	}

	public function testValidate_WithValidData_ShouldReturnTrue(): void
	{
		$request = new TestRequest();
		$request->buildForm([
			'name' => 'Dalibor Korpar',
			'email' => 'dalibor.korpar@gmail.com',
		]);

		$request->validate();
		$request->getForm()->onSubmit();
		$this->assertEquals($request->name, 'Dalibor Korpar');
	}

	public function testItemsNotJsonRequest(): void
	{
		$this->expectExceptionMessage('Only Json is allowed for setting items on select in request!');
		$req = new TestItemsNotJsonRequest();
		$req->buildForm();
	}

	public function testArrayRequest(): void
	{
		$json = '{"items":[{"name":"first", "name": null, "name": "bla"}]}';
		$request = new TestArrayRequest();
		$inputData = Json::decode($json, Json::FORCE_ARRAY);
		$request->buildForm($inputData);
		$request->validate();
		$this->assertTrue(true);
	}

	public function testItems(): void
	{
		$request = new TestItemsRequest();
		$request->buildForm();
		$items = $request->getForm()->getComponent('rating')->getItems();
		$this->assertEquals([1 => 1, 2, 3, 4, 5], $items);
	}

}
