<?php declare (strict_types = 1);

namespace Wedo\Api\Helpers;

use DateTimeImmutable;
use Nette\InvalidArgumentException;
use ReflectionNamedType;

class RequestHydrator
{

	public static function hydrateValue(ReflectionNamedType $type, mixed $value): string|int|float|bool|DateTimeImmutable|null
	{
		if (!$type->isBuiltin() && $type->getName() !== 'DateTimeInterface') {
			throw new InvalidArgumentException('Only built in types are supported! ' . $type->getName() . ' is not supported');
		}

		return self::castValueToBuiltInType($value, $type->getName(), $type->allowsNull());
	}

	public static function castValueToBuiltInType(mixed $value, string $type, bool $allowsNull): int|float|string|bool|DateTimeImmutable|null
	{
		switch ($type) {
			case 'int':
				return (int) (is_numeric($value) ? $value : 0);
			case 'float':
				return (float) (is_numeric($value) ? $value : 0);
			case 'bool':
				return (bool) $value;
			case 'DateTimeInterface':
				if ($value === null || (is_string($value) && trim($value) === '') && $allowsNull) {
					return null;
				}

				return new DateTimeImmutable($value); //@phpstan-ignore-line
			default:
				return (string) $value; //@phpstan-ignore-line
		}
	}

}
