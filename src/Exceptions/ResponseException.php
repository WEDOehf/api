<?php declare(strict_types = 1);

namespace Wedo\Api\Exceptions;

use Exception;
use Nette\Localization\ITranslator;
use Throwable;

class ResponseException extends Exception
{

	/** @var string[] */
	protected array $parameters;

	/** @var ResponseException[] */
	protected array $additionalExceptions = [];

	/**
	 * @param mixed|string ...$parameters
	 */
	public function __construct(string $message = '', int $code = 500, ?Throwable $previous = null, ...$parameters)
	{
		$this->parameters = $parameters;
		parent::__construct($message, $code, $previous);
	}


	public function getTranslatedMessage(ITranslator $translator): string
	{
		return $translator->translate($this->getMessage(), $this->parameters);
	}

	public function addAdditionalException(ResponseException $exception): void
	{
		$this->additionalExceptions[] = $exception;
	}

	/**
	 * @return ResponseException[]
	 * @codeCoverageIgnore
	 */
	public function getAdditionalExceptions(): array
	{
		return $this->additionalExceptions;
	}

	/**
	 * @return ResponseException[]
	 */
	public function getAll(): array
	{
		return [...[$this], ...$this->getAdditionalExceptions()];
	}


	/**
	 * @return string[]
	 */
	public function getParameters(): array
	{
		return $this->parameters;
	}

}
