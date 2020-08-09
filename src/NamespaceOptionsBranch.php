<?php namespace EugeneErg\Preparer;

use Closure;
use Exception;
use ReflectionException;
use ReflectionFunction;

class NamespaceOptionsBranch extends Branch
{
    /**
     * @var Closure[][][][]
     */
    private $converters = [];

    /**
     * @var Closure[][]
     */
    private $generators = [];

    /**
     * @param Closure $converter
     * @throws ReflectionException
     */
    public function addConverter(Closure $converter): void
    {
        $function = new ReflectionFunction($converter);
        $typeTo = $function->getReturnType()->getName();
        $typeFrom = $function->getParameters()[0]->getType()->getName();
        $this->converters[$typeFrom][$typeTo] = (object) [
            'method' => $converter,
            'values' => [],
        ];
    }

    /**
     * @param Closure $generator
     * @throws ReflectionException
     */
    public function addGenerator(Closure $generator): void
    {
        $function = new ReflectionFunction($generator);
        $type = $function->getReturnType()->getName();
        $this->generators[$type] = (object) [
            'method' => $generator,
        ];
    }

    /**
     * @param $value
     * @param string $type
     * @param string $method
     * @param string $parameter
     * @return mixed
     * @throws Exception
     */
    public function convert($value, string $type, string $method, string $parameter)
    {
        $valueType = is_object($value) ? get_class($value) : gettype($value);

        if ($valueType === $type) {
            return $value;
        }

        $converter = $this->converters[$valueType][$type][$method][$parameter]
            ?? $this->converters[$valueType][$type][$method][null]
            ?? $this->converters[$valueType][$type][null][null]
            ?? null;

        if (!isset($converter)) {
            throw new Exception();
        }

        $valueKey = ValueIndexService::instance()->getIndex($value);

        if (!isset($converter->values[$valueKey])) {
            $method = $converter->method;
            $converter->values[$valueKey] = $method($value, $method, $parameter);
        }

        return $converter->values[$valueKey];
    }

    /**
     * @param string $type
     * @param string $class
     * @param string $method
     * @param string $parameter
     * @return mixed
     * @throws Exception
     */
    public function generate(string $type, string $class, string $method, string $parameter)
    {
        if (!isset($this->generators[$type])) {
            throw new Exception();
        }

        $generator = $this->generators[$type];

        if (!property_exists($generator, 'value')) {
            $method = $generator->method;
            $generator->value = $method($class, $method, $parameter);
        }

        return $generator->value;
    }
}
