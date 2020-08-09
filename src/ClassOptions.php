<?php

namespace EugeneErg\Preparer;

use Closure;
use Exception;
use ReflectionFunction;
use ReflectionException;

class ClassOptions
{
    /**
     * @var Closure[][][][]
     */
    private $converters = [];

    /**
     * @var Closure[][][]
     */
    private $generators = [];

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var string|null
     */
    private $method;

    /**
     * @var string|null
     */
    private $parameter;

    /**
     * ClassOptions constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @param Closure $converter
     * @param string|null $method
     * @param string|null $parameter
     * @throws ReflectionException
     */
    public function addConverter(Closure $converter, string $method = null, string $parameter = null): void
    {
        $function = new ReflectionFunction($converter);
        $typeTo = $function->getReturnType()->getName();
        $typeFrom = $function->getParameters()[0]->getType()->getName();
        $this->parameters[$typeFrom][$typeTo][$method][$parameter] = (object) [
            'method' => $converter,
            'values' => [],
        ];
    }

    /**
     * @param Closure $generator
     * @param string|null $method
     * @param string|null $parameter
     * @throws ReflectionException
     */
    public function addGenerator(Closure $generator, string $method = null, string $parameter = null): void
    {
        $function = new ReflectionFunction($generator);
        $type = $function->getReturnType()->getName();
        $this->generators[$type][$method][$parameter] = (object) [
            'method' => $generator,
        ];
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

        $converter = $this->getExistValue(
            $this->converters,
            [$valueType, $type, $method, $parameter]
        );

        if (!isset($converter)) {
            throw new Exception();
        }

        $valueKey = ValueIndexService::instance()->getIndex($value);

        if (!isset($converter->values[$valueKey])) {
            $method = $converter->method;
            $converter->values[$valueKey] = $method($value, $class, $method, $parameter);
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
        $generator = $this->getExistValue(
            $this->generators,
            [$type, $method, $parameter]
        );

        if (!isset($generator)) {
            throw new Exception();
        }

        if (!property_exists($generator, 'value')) {
            $method = $generator->method;
            $generator->value = $method($class, $method, $parameter);
        }

        return $generator->value;
    }

    /**
     * @param array $array
     * @param string[] $keys
     * @param int $staticCount
     * @return mixed
     */
    private function getExistValue(array $array, array $keys, int $staticCount = 0)
    {
        for ($attempt = 0; $attempt <= count($keys) - $staticCount; $attempt++) {
            $currentKeys = array_replace(
                $keys,
                array_fill(count($keys) - $attempt, $attempt, null)
            );
            $result = $this->getByKeys($array, $currentKeys);

            if ($result !== null) {
                return $result;
            }
        }

        return null;
    }

    /**
     * @param array $array
     * @param array $keys
     * @return mixed
     */
    private function getByKeys(array $array, array $keys)
    {
        $current = $array;

        foreach ($keys as $key) {
            if (!isset($current[$key])) {
                return null;
            }

            $current = $current[$key];
        }

        return $current;
    }
}