<?php namespace EugeneErg\Preparer\SQL\Functions\Traits;

use EugeneErg\Preparer\SQL\Functions\NotFunction;

trait NumericFunctionTrait
{
    use AggregateFunctionTrait;

    protected ?string $type = 'numeric';

    public function sum(bool $distinct = false): NotFunction
    {
        /** @var NotFunction $result */
        $result = $this->call('sum', [$distinct]);

        return $result;
    }
}
