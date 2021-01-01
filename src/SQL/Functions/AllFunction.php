<?php namespace EugeneErg\Preparer\SQL\Functions;

use EugeneErg\Preparer\SQL\Functions\Traits\AllFunctionTrait;
use ArrayAccess;
use EugeneErg\Preparer\ValueInterface;

class AllFunction implements ArrayAccess, ValueInterface
{
    use AllFunctionTrait;

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
