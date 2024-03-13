<?php declare(strict_types = 1);

namespace Wedo\Api\Exceptions;

use Exception;
use Nette\Localization\Translator;
use Throwable;

class ResponseException extends Exception
{

	/** @var mixed[] */
	protected array $parameters;

	/** @var ResponseException[] */
	protected array $additionalExceptions = [];

	public function __construct(string $message = '', int $code = 500, ?Throwable $previous = null, mixed ...$parameters)
	{
		$this->parameters = $parameters;

		parent::__construct($message, $code, $previous);
	}

	public function getTranslatedMessage(Translator $translator): string
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
	 * @return mixed[]
	 */
	public function getParameters(): array
	{
		return $this->parameters;
	}

}
