<?php namespace EugeneErg\Preparer\SQL\Functions;

use EugeneErg\Preparer\SQL\Functions\Traits\AggregateFunctionTrait;
use EugeneErg\Preparer\ValueInterface;

class NumericFunction implements ValueInterface
{
    use AggregateFunctionTrait;
}
