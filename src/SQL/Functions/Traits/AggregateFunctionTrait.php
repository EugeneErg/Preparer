<?php namespace EugeneErg\Preparer\SQL\Functions\Traits;

use EugeneErg\Preparer\SQL\Functions\NotFunction;

trait AggregateFunctionTrait
{
    use FunctionTrait;

    public function count(bool $distinct = false): NotFunction
    {
        /** @var NotFunction $result */
        $result = $this->call('count', [$distinct]);

        return $result;
    }

}
