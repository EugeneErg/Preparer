<?php namespace EugeneErg\Preparer\SQL\Functions;

use EugeneErg\Preparer\SQL\Functions\Traits\StringFunctionTrait;
use ArrayAccess;
use EugeneErg\Preparer\ValueInterface;

class StringFunction extends AbstractFunction implements ArrayAccess, ValueInterface
{
    use StringFunctionTrait;

    public function offsetGet($offset): StringFunction
    {
        /** @var StringFunction $result */
        $result = $this->getChildren(StringFunction::class, 'offset', $offset);

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
