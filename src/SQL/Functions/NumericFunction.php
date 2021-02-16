<?php namespace EugeneErg\Preparer\SQL\Functions;

use EugeneErg\Preparer\SQL\Functions\Traits\AggregateFunctionTrait;
use EugeneErg\Preparer\ValueInterface;

class NumericFunction extends AbstractFunction implements ValueInterface
{
    use AggregateFunctionTrait;
}
