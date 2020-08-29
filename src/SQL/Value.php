<?php namespace EugeneErg\Preparer\SQL;

use ArrayAccess;
use EugeneErg\Preparer\Exception\InvalidActionException;
use EugeneErg\Preparer\SQL\Records\ValueFunctionRecord;

/**
 * @mixin FunctionContainer
 */
class Value implements ArrayAccess
{
    /**
     * @var mixed
     */
    private $object;
    private ValueFunctionRecord $valueRecord;

    /**
     * @param mixed $object
     */
    public function __construct($object)
    {
        $this->object = $object;
        $this->valueRecord = new ValueFunctionRecord($this);
    }

    public function getObject()
    {
        return $this->object;
    }

    public function __get(string $name): FunctionContainer
    {
        return $this->valueRecord->getContainer()->$name;
    }

    public function __call(string $name, array $arguments): FunctionContainer
    {
        return $this->valueRecord->getContainer()->$name(...$arguments);
    }

    public function offsetGet($name): FunctionContainer
    {
        return $this->valueRecord->getContainer()[$name];
    }

    /**
     * @param string|int $offset
     * @return bool
     * @throws InvalidActionException
     */
    public function offsetExists($offset): bool
    {
        throw new InvalidActionException();
    }

    /**
     * @param string|int $offset
     * @throws InvalidActionException
     */
    public function offsetUnset($offset): void
    {
        throw new InvalidActionException();
    }

    /**
     * @param string|int $offset
     * @param mixed $value
     * @throws InvalidActionException
     */
    public function offsetSet($offset, $value): void
    {
        throw new InvalidActionException();
    }
}
