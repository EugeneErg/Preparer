<?php namespace EugeneErg\Preparer\SQL;

use ArrayAccess;
use EugeneErg\Preparer\Exception\InvalidActionException;
use EugeneErg\Preparer\SQL\Functions\Traits\ArrayFunctionTrait;
use EugeneErg\Preparer\SQL\Query\AbstractSource;

class Value extends AbstractSource implements ArrayAccess
{
    use ArrayFunctionTrait;

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
