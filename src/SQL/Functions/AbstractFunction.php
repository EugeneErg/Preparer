<?php namespace EugeneErg\Preparer\SQL\Functions;

use EugeneErg\Preparer\SQL\Functions\Traits\FunctionTrait;
use EugeneErg\Preparer\SQL\ValueInterface;

abstract class AbstractFunction implements ValueInterface
{
    use FunctionTrait;

    public function __toString(): string
    {

    }
}