<?php namespace EugeneErg\Preparer;

use Closure;
use Exception;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionParameter;
use function is_string;
use function Sodium\compare;

final class ClassCreatorService
{
    /**
     * @var array
     */
    private $generators = [];

    /**
     * @var array
     */
    private $converters = [];

    /**
     * @var Closure[][]
     */
    private $generatorsByNameSpace = [];

    /**
     * @var Closure[][][]
     */
    private $convertersByNameSpace = [];

    /**
     * @var array
     */
    private $indexes = [];

    /**
     * @var Object[][]
     */
    private $classes = [];

    /**
     * @var array
     */
    private $functions = [];

    /**
     * @var self[]
     */
    private static $instance;

    /**
     * @var ClassComparisonService
     */
    private $classComparisonService;

    private function __construct()
    {
        $this->classComparisonService = new ClassComparisonService();
    }

    /**
     * @return static
     */
    public static function instance(): self
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param string $className
     * @param array $arguments
     * @return mixed
     * @throws ReflectionException
     * @throws Exception
     */
    public function create(string $className, array $arguments = [])
    {
        $class = new ReflectionClass($className);
        $constructor = $class->getConstructor();

        if (!$constructor) {
            return new $className();
        }

        return $class->newInstanceArgs(
            $this->convertArgumentsAccordingToParameters(
                $arguments,
                $constructor->getParameters(),
                $className,
                $constructor->getName()
            )
        );
    }

    /**
     * @param string $className
     * @param array $arguments
     * @return mixed
     * @throws ReflectionException
     * @throws Exception
     */
    public function createSingle(string $className, array $arguments = [])
    {
        $class = new ReflectionClass($className);
        $constructor = $class->getConstructor();
        $parameters = $constructor
            ? $this->convertArgumentsAccordingToParameters(
                $arguments,
                $constructor->getParameters(),
                $className,
                $constructor->getName()
            )
            : [];


        $index = ValueIndexService::instance()->getIndex(...$parameters);

        if (!isset($this->classes[$className][$index])) {
            $this->classes[$className][$index] = $class->newInstanceArgs($parameters);
        }

        return $this->classes[$className][$index];
    }

    /**
     * @param callable $function
     * @param array $arguments
     * @return mixed
     * @throws ReflectionException
     * @throws Exception
     */
    public function call(callable $function, array $arguments = [])
    {
        $reflectionFunction = $this->getReflectionByCallable($function);

        list($class, $method) = $this->getClassMethodByCallable($function);

        return $reflectionFunction->invokeArgs(
            $this->convertArgumentsAccordingToParameters(
                $arguments,
                $reflectionFunction->getParameters(),
                $class,
                $method
            )
        );
    }

    /**
     * @param callable $function
     * @param array $arguments
     * @return mixed
     * @throws ReflectionException
     * @throws Exception
     */
    public function callSingle(callable $function, array $arguments = [])
    {
        $functionIndex = $this->getIndexByCallable($function);
        $reflectionFunction = $this->getReflectionByCallable($function);

        list($class, $method) = $this->getClassMethodByCallable($function);

        $parameters = $this->convertArgumentsAccordingToParameters(
            $arguments,
            $reflectionFunction->getParameters(),
            $class,
            $method
        );
        $index = ValueIndexService::instance()->getIndex(...$parameters);

        if (!isset($this->functions[$functionIndex][$index])) {
            $this->functions[$functionIndex][$index] = $reflectionFunction->invokeArgs($parameters);
        }

        return $this->functions[$functionIndex][$index];
    }

    /**
     * @param $value
     * @param string $type
     * @param string $class
     * @param string $method
     * @param string $parameter
     * @return mixed
     * @throws Exception
     */
    public function convert($value, string $type, string $class, string $method, string $parameter)
    {
        $valueType = is_object($value) ? get_class($value) : gettype($value);

        if ($valueType === $type) {
            return $value;
        }

        $converter = $this->getPriorityConverter($type, $class, $method, $parameter, $valueType);

        if ($converter === null) {
            return $this->convertByNameSpace($value, $type, $class, $method, $parameter);
        }

        $valueIndex = ValueIndexService::instance()->getIndex($value);

        if (!isset($converter->values[$valueIndex])) {
            $method = $converter->method;
            $converter->values[$valueIndex] = $method($value, $class, $method, $parameter);
        }

        return $converter->values[$valueIndex];
    }

    private function getPriorityConverter(
        string $type,
        string $class,
        string $method,
        string $parameter,
        string $valueType = null
    ): ?object {
        if (!isset($this->classes[$type])) {
            return null;
        }

        $classes = [];

        foreach ($this->classes[$type] as $className => $classB) {
            if (!isset($classB->converters[$valueType])) {
                continue;
            }

            $level = $this->classComparisonService->getAffinityLevel($class, $className);

            if ($level !== null) {
                $classB->level = $level;
                $classes[] = $classB;
            }
        }

        usort($classes, function(object $classA, object $classB) use($valueType, $method, $parameter): int {
            return $this->compare(
                    isset($classB->converters[$valueType][$method][$parameter]),
                    isset($classA->converters[$valueType][$method][$parameter])
                ) ?? $this->compare(
                    isset($classB->converters[$valueType][$method][null]),
                    isset($classA->converters[$valueType][$method][null])
                ) ?? $this->compare(
                    isset($classB->converters[$valueType][null][null]),
                    isset($classA->converters[$valueType][null][null])
                ) ?? $this->compare(
                    $classA->level,
                    $classB->level
                ) ?? 0;
        });

        return $classes[0]->converters[$valueType][$method][$parameter]
            ?? $classes[0]->converters[$valueType][$method][null]
            ?? $classes[0]->converters[$valueType][null][null]
            ?? null;
    }

    /**
     * @param $valueA
     * @param $valueB
     * @return int
     */
    private function compare($valueA, $valueB): ?int
    {
        $result = $valueA <=> $valueB;

        return $result === 0 ? null : $result;
    }

    /**
     * @param string $type
     * @param string $class
     * @param string $method
     * @param string $parameter
     * @throws Exception
     * @return mixed
     */
    private function createDefaultValue(string $type, string $class, string $method, string $parameter)
    {
        $generator = $this->getPriorityConverter($type, $class, $method, $parameter);

        if ($generator === null) {
            return $this->createDefaultValueByNameSpace($type, $class, $method, $parameter);
        }

        if (!property_exists($generator, 'value')) {
            $method = $generator->method;
            $generator->value = $method($class, $method, $parameter);
        }

        return $generator->value;
    }

    /**
     * @param $value
     * @param string $type
     * @param string $class
     * @param string $method
     * @param string $parameter
     * @return mixed
     */
    private function convertByNameSpace($value, string $type, string $class, string $method, string $parameter)
    {
        $valueType = is_object($value) ? get_class($value) : gettype($value);
        $routes = explode('\\', $class);

        for ($i = 0; $i < count($routes); $i++) {

        }


        throw new Exception('can\'t convert to type: ' . $type);
    }

    /**
     * @param string $type
     * @param string $class
     * @param string $method
     * @param string $parameter
     * @return mixed
     */
    private function createDefaultValueByNameSpace(string $type, string $class, string $method, string $parameter)
    {
        if (class_exists($type)) {
            return $this->createSingle($type);
        }

        throw new Exception('need variable type: ' . $type);
    }

    /**
     * @param array $arguments
     * @param ReflectionParameter[] $parameters
     * @param string $className,
     * @param string $methodName
     * @return array
     * @throws Exception
     */
    private function convertArgumentsAccordingToParameters(
        array $arguments,
        array $parameters,
        string $className,
        string $methodName
    ): array {
        $result = [];

        foreach (array_reverse($parameters) as $number => $parameter) {
            $name = $parameter->getName();
            $value = $arguments[$name] ?? $arguments[$number] ?? null;
            $exists = array_key_exists($name, $arguments) || array_key_exists($number, $arguments);

            if (!$exists && !$parameter->isDefaultValueAvailable()) {
                if (!$parameter->isVariadic()) {
                    if ($parameter->allowsNull() || !$parameter->hasType()) {
                        $result[] = $value;
                    } elseif ($parameter->hasType()) {
                        $result[] = $this->createDefaultValue(
                            $parameter->getType()->getName(),
                            $className,
                            $methodName,
                            $name
                        );
                    }
                }
            } elseif (!$exists && $parameter->isDefaultValueAvailable()) {
                if (count($result)) {
                    $result[] = $parameter->getDefaultValue();
                }
            } else {
                $values = is_array($value) && $parameter->isVariadic() ? array_reverse($value) : [$value];

                if ($parameter->hasType()) {
                    $type = $parameter->getType()->getName();
                } elseif ($parameter->isDefaultValueAvailable()) {
                    $type = gettype($parameter->getDefaultValue());
                } else {
                    foreach ($values as $value) {
                        $result[] = $value;
                    }

                    continue;
                }

                foreach ($values as $value) {
                    $result[] = $this->convert(
                        $value,
                        $type,
                        $className,
                        $methodName,
                        $name
                    );
                }
            }
        }

        return array_reverse($result);
    }

    /**
     * @param callable $callback
     * @param string|null $nameSpace
     * @throws ReflectionException
     */
    public function addGenerator(
        callable $callback,
        string $nameSpace = null
    ): void {
        $function = $this->getReflectionByCallable($callback);
        $type = $function->getReturnType()->getName();
        $current = &$this->defaultGeneratorsByNameSpace[$type];
        $routes = $nameSpace === null ? [$nameSpace] : explode('\\', $nameSpace);

        foreach ($routes as $route) {
            if (!isset($current)) {
                $current = (object)[
                    'children' => [],
                ];
            }

            if ($nameSpace !== null) {
                $current = &$current[$route]->children[$route];
            }
        }

        $current->callback = $function->getClosure();
    }

    /**
     * @param callable $callback
     * @param string $class
     * @param string|null $method
     * @param string|null $parameter
     * @throws ReflectionException
     */
    public function addGeneratorWithClass(
        callable $callback,
        string $class,
        string $method = null,
        string $parameter = null
    ): void {
        $function = $this->getReflectionByCallable($callback);
        $type = $function->getReturnType()->getName();
        $this->classes[$type][$class][null][$method][$parameter] = (object) [
            'method' => $function->getClosure(),
        ];
    }

    /**
     * @param callable $callback
     * @param string|null $class
     * @param string|null $method
     * @param string|null $parameter
     * @throws ReflectionException
     */
    public function addConverterWithClass(
        callable $callback,
        string $class,
        string $method = null,
        string $parameter = null
    ): void {
        $function = $this->getReflectionByCallable($callback);
        $typeTo = $function->getReturnType()->getName();
        $typeFrom = $function->getParameters()[0]->getType()->getName();
        $this->converters[$typeFrom][$typeTo][$class][$method][$parameter] = (object) [
            'method' => $function->getClosure(),
            'values' => [],
        ];
    }

    /**
     * @param callable $callback
     * @param string|null $nameSpace
     * @throws ReflectionException
     */
    public function addConverter(
        callable $callback,
        string $nameSpace = null
    ): void {
        $function = $this->getReflectionByCallable($callback);
        $typeTo = $function->getReturnType()->getName();
        $typeFrom = $function->getParameters()[0]->getType()->getName();
        $current = &$this->convertersByNameSpace[$typeFrom][$typeTo];
        $routes = $nameSpace === null ? [$nameSpace] : explode('\\', $nameSpace);

        foreach ($routes as $route) {
            if (!isset($current)) {
                $current = (object)[
                    'children' => [],
                ];
            }

            if ($nameSpace !== null) {
                $current = &$current[$route]->children[$route];
            }
        }

        $current->callback = $function->getClosure();
    }

    /**
     * @param Closure $closure
     * @return string
     * @throws ReflectionException
     */
    public function getClosureClassId(Closure $closure): string
    {
        $reflection = new ReflectionFunction($closure);

        return md5(implode(',', [
            $reflection->__toString(),
            $reflection->getFileName(),
            $reflection->getDocComment(),
            $reflection->getStartLine(),
        ]));
    }

    /**
     * @param callable $function
     * @return string[]|null[]
     * @throws Exception
     */
    private function getClassMethodByCallable(callable $function): array
    {
        if (is_string($function)) {
            $method = explode('::', $function);

            if (isset($method[1])) {
                return $method;
            }

            return [null, $method[0]];
        }

        if ($function instanceof Closure) {
            return [null, $this->getClosureClassId($function)];
        }

        if (is_object($function)) {
            return [get_class($function), '__invoke'];
        }

        if (is_array($function)) {
            return [is_object($function[0]) ? get_class($function[0]) : $function[0], $function[1]];
        }

        return [null, null];
    }

    /**
     * @param callable $function
     * @throws ReflectionException
     */
    private function getReflectionByCallable(callable $function): ReflectionFunction
    {
        return new ReflectionFunction(Closure::fromCallable($function));
    }

    /**
     * @param callable $function
     * @return string
     * @throws Exception
     */
    private function getIndexByCallable(callable $function): string
    {
        if (is_object($function)) {
            return spl_object_hash($function);
        }

        if (is_string($function)) {
            return $function;
        }

        if (is_object($function[0])) {
            return spl_object_hash($function[0]) . '::' . $function[1];
        }

        return implode('::', $function);
    }
}

class qwe2 {
}

class qwe {
    private $qwe2;
    private $val;
    private $date;

    function __construct(qwe2 $qwe2, string $val, \DateTimeImmutable $date)
    {
        $this->qwe2 = $qwe2;
        $this->val = $val;
        $this->date = $date;
    }
}

ClassCreatorService::instance('test')->addConverter(function(string $value): \DateTimeImmutable {
    return new \DateTimeImmutable($value);
});

var_dump(ClassCreatorService::instance('test')->createSingle(qwe::class, ['val' => 'test', 'date' => '2020-03-21']));
var_dump(ClassCreatorService::instance('test')->createSingle(qwe::class, [1 => 'thfg']));
//var_dump($q);