<?php declare(strict_types = 1);

namespace Wedo\Api\DI;

use Nette\Application\Routers\Route;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\Utils\Strings;
use ReflectionClass;
use ReflectionMethod;
use Wedo\Api\Attributes\Internal;
use Wedo\Api\Routing\RouterFactory;

/**
 * @codeCoverageIgnore To hard to test ATM and this class is not likely to change
 */
class ApiExtension extends CompilerExtension
{

	/** @var ApiExtensionConfig */
	protected $config; //phpcs:ignore

	public function __construct()
	{
		$this->config = new ApiExtensionConfig();
	}

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		/** @var ServiceDefinition $routerFactory */
		$routerFactory = $builder->addDefinition($this->prefix('routerFactory'));
		$routerFactory->setFactory(RouterFactory::class);
	}

	public function beforeCompile(): void
	{
		$apiEndpoints = [];
		$types = $this->getContainerBuilder()->findByType($this->config->controller);
		$apiRootNamespace = (new ReflectionClass($this->config->controller))->getNamespaceName();
		$removeCharactersFromStart = strlen($apiRootNamespace) + 1;

		foreach ($types as $type) {
			/** @var class-string $typeString */
			$typeString = $type->getType();
			$obj = new ReflectionClass($typeString);
			$path = '';
			$module = '';

			if ($apiRootNamespace !== $obj->getNamespaceName()) {
				$relativeNamespace = substr($obj->getNamespaceName(), $removeCharactersFromStart);
				$path = Strings::lower($relativeNamespace) . '/';
				$module = str_replace('\\', ':', $relativeNamespace) . ':';
			}

			$className = Strings::before($obj->getShortName(), 'Controller') ?? '';
			$path .= Route::presenter2path($className);
			$publicMethods = $obj->getMethods(ReflectionMethod::IS_PUBLIC);

			foreach ($publicMethods as $oneMethod) {
				if ($oneMethod->isConstructor() ||
					count($oneMethod->getAttributes(Internal::class)) > 0 ||
					$oneMethod->getDeclaringClass()->getName() !== $obj->getName()) {
					continue;
				}

				$endPointUrl = $path . '/' . Route::action2path($oneMethod->getName());
				$apiEndpoints[$endPointUrl] = $module . $className;
			}
		}

		/** @var ServiceDefinition $def */
		$def = $this->getContainerBuilder()->getDefinition($this->prefix('routerFactory'));
		$def->getFactory()->arguments['url'] = $this->config->url;
		$def->getFactory()->arguments['controllerNamespace'] = $this->config->controllerNameNamespace;
		$def->getFactory()->arguments['apiEndPoints'] = $apiEndpoints;
	}

}
