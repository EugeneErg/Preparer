<?php namespace EugeneErg\Preparer\SQL\Functions;

use EugeneErg\Preparer\SQL\Functions\Traits\AllFunctionTrait;
use ArrayAccess;

class AllFunction extends AbstractFunction implements ArrayAccess
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
