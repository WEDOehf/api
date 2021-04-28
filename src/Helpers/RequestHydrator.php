<?php declare (strict_types = 1);

namespace Wedo\Api\Helpers;

use DateTimeImmutable;
use Nette\InvalidArgumentException;
use ReflectionNamedType;

class RequestHydrator
{

	/**
	 * @param mixed|string $value
	 * @return string|int|float|bool|DateTimeImmutable|null
	 */
	public static function hydrateValue(ReflectionNamedType $type, $value)
	{
		if (!$type->isBuiltin() && $type->getName() !== 'DateTimeInterface') {
			throw new InvalidArgumentException('Only built in types are supported! ' . $type->getName() . ' is not supported');
		}

		return self::castValueToBuiltInType($value, $type->getName(), $type->allowsNull());
	}

	/**
	 * @param mixed $value
	 * @return int|float|string|bool|DateTimeImmutable|null
	 */
	public static function castValueToBuiltInType($value, string $type, bool $allowsNull)
	{
		switch ($type) {
			case 'int':
				return (int) $value;
			case 'float':
				return (float) $value;
			case 'bool':
				return (bool) $value;
			case 'DateTimeInterface':
				if ($value === '' && $allowsNull) {
					return null;
				}

				return new DateTimeImmutable($value);
			default:
				return (string) $value;
		}
	}

}
