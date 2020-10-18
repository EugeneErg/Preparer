<?php namespace EugeneErg\Preparer\SQL\Functions\Traits;

use EugeneErg\Preparer\SQL\Functions\AllFunction;
use EugeneErg\Preparer\SQL\Functions\Traits\Methods\lengthMethod;

trait ArrayFunctionTrait
{
    use AggregateFunctionTrait;

    use lengthMethod;

    /**
     * @param int|string $offset
     * @return AllFunction
     */
    public function offsetGet($offset): AllFunction
    {
        /** @var AllFunction $result */
        $result = $this->getChildren(AllFunction::class, 'offset', (string) $offset);

        return $result;
    }

    public function __get(string $get): AllFunction
    {
        /** @var AllFunction $result */
        $result = $this->getChildren(AllFunction::class, 'get', $get);

        return $result;
    }
}
