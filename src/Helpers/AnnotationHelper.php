<?php declare (strict_types = 1);

namespace Wedo\Api\Helpers;

use Nette\Reflection\AnnotationsParser;
use Nette\Reflection\IAnnotation;
use Reflector;

class AnnotationHelper
{

	/**
	 * Returns an annotation value.
	 *
	 * @return string|IAnnotation
	 */
	public static function getAnnotation(Reflector $reflector, string $name)
	{
		$res = AnnotationsParser::getAll($reflector);
		return isset($res[$name]) ? end($res[$name]) : null;
	}

}
