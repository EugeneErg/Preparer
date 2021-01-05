<?php namespace EugeneErg\Preparer\SQL;

use ArrayAccess;
use EugeneErg\Preparer\Exception\InvalidActionException;
use EugeneErg\Preparer\SQL\Query\AbstractSource;

class Value extends AbstractSource implements ArrayAccess
{
    /**
     * @var mixed
     */
    private $object;

    /**
     * @param mixed $object
     */
    public function __construct($object)
    {
        $this->object = $object;
        parent::__construct();
    }

    /**
     * @return mixed
     */
    public function getObject()
    {
        return $this->object;
    }

    public function __get(string $name): self
    {
        return $this->offsetGet($name);
    }

    /**
     * @param string|int $name
     * @return $this
     */
    public function offsetGet($name): self
    {
        $object = $this->object;

        if (is_array($object)) {
            $object = $object[$name];
        } elseif (is_object($object)) {
            $object = $object->$name;
        } else {
            $object = null;
        }

        return new Value($object);
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
