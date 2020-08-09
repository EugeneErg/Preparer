<?php namespace EugeneErg\Preparer\Parser;

use ReflectionMethod;

/**
 * Class AbstractTemplate
 * @package EugeneErg\Preparer\Parser
 */
abstract class AbstractTemplate
{
    public const TEMPLATE = '(.*)';

    /**
     * @var array
     */
    private $values = [];

    /**
     * AbstractTemplate constructor.
     */
    public function __construct()
    {
        $arguments = func_get_args();
        $parameters = (new ReflectionMethod($this, '__construct'))->getParameters();

        foreach ($parameters as $number => $parameter) {
            if (array_key_exists($number, $arguments)) {
                $this->values[$parameter->getName()] = $arguments[$number];
            } elseif ($parameter->isOptional() && !$parameter->isVariadic()) {
                $this->values[$parameter->getName()] = $parameter->getDefaultValue();
            } else {
                break;
            }
        }
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->values[$name];
    }
}
