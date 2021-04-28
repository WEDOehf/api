<?php declare (strict_types = 1);

namespace Wedo\Api\Tests;

use Nette\Localization\ITranslator;
use PHPUnit\Framework\TestCase;
use Wedo\Api\Exceptions\NotFoundException;
use Wedo\Api\Exceptions\ResponseException;
use Wedo\Api\Exceptions\UnauthorizedException;

class ExceptionsTest extends TestCase
{

	public function testResponseException(): void
	{
		$exception = new ResponseException('test %i', 500, null, '1');
		$exception->addAdditionalException(new ResponseException('test 2'));
		$this->assertCount(2, $exception->getAll());
		$this->assertCount(1, $exception->getAdditionalExceptions());
		$this->assertCount(1, $exception->getParameters());

		$translator = $this->createMock(ITranslator::class);
		$translator->expects($this->once())->method('translate')->with('test %i', ['1'])->willReturn('test 1');

		$this->assertEquals('test 1', $exception->getTranslatedMessage($translator));
	}

	public function testUnathorizedException(): void
	{
		$exception = new UnauthorizedException();
		$this->assertEquals('Operation not permitted!', $exception->getMessage());

		$exception = new UnauthorizedException('Not allowed');
		$this->assertEquals('Not allowed', $exception->getMessage());
	}

	public function testNotFoundException(): void
	{
		$exception = new NotFoundException();
		$this->assertEquals('Action does not exist!', $exception->getMessage());
	}

}
