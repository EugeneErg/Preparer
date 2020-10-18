<?php namespace EugeneErg\Preparer\SQL\Functions;

use EugeneErg\Preparer\SQL\Functions\Traits\ArrayFunctionTrait;
use EugeneErg\Preparer\SQL\Functions\Traits\NumericFunctionTrait;
use EugeneErg\Preparer\SQL\Functions\Traits\StringFunctionTrait;
use ArrayAccess;

class AllFunction implements ArrayAccess
{
    use ArrayFunctionTrait, StringFunctionTrait, NumericFunctionTrait;

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
