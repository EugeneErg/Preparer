<?php namespace EugeneErg\Preparer\SQL\Functions\Traits;

use EugeneErg\Preparer\SQL\Functions\Traits\Methods\lengthMethod;

trait StringFunctionTrait
{
    use AggregateFunctionTrait;

    use lengthMethod;

    protected ?string $type = 'string';
}