<?php namespace EugeneErg\Preparer\SQL\Functions\Traits\Methods;

use EugeneErg\Preparer\SQL\Functions\NumericFunction;
use EugeneErg\Preparer\SQL\Functions\Traits\FunctionTrait;

trait lengthMethod
{
    use FunctionTrait;

    public function length(): NumericFunction
    {
        /** @var NumericFunction $result */
        $result = $this->call( 'length');

        return $result;
    }
}