<?php namespace EugeneErg\Preparer\SQL\Functions;

use EugeneErg\Preparer\SQL\Functions\Traits\ArrayFunctionTrait;
use EugeneErg\Preparer\ValueInterface;

class ArrayFunction implements \ArrayAccess, ValueInterface
{
    use ArrayFunctionTrait;

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
