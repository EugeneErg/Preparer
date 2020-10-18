<?php namespace EugeneErg\Preparer\SQL\Functions;

use EugeneErg\Preparer\SQL\Functions\Traits\StringFunctionTrait;
use ArrayAccess;

class StringFunction implements ArrayAccess
{
    use StringFunctionTrait;

    public function offsetGet($offset): StringFunction
    {
        /** @var StringFunction $result */
        $result = $this->getChildren('offset', $offset);

        return $result;
    }

    public function offsetExists($offset): bool
    {

    }

    public function offsetSet($offset, $value): void
    {

    }

    public function offsetUnset($offset): void
    {

    }
}
