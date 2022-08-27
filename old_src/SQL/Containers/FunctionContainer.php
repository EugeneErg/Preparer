<?php namespace EugeneErg\Preparer\SQL\Containers;

use EugeneErg\Preparer\Container;

class FunctionContainer extends Container
{
    /**
     * @param bool $distinct
     * @return FunctionContainer
     */
    public function count(bool $distinct = false): FunctionContainer
    {
        /** @var FunctionContainer $result */
        $result = $this->__call('count', [$distinct]);

        return $result;
    }

    /**
     * @param bool $distinct
     * @return FunctionContainer
     */
    public function sum(bool $distinct = false): FunctionContainer
    {
        /** @var FunctionContainer $result */
        $result = $this->__call('sum', [$distinct]);

        return $result;
    }

    /**
     * @param bool $distinct
     * @return FunctionContainer
     */
    public function avg(bool $distinct = false): FunctionContainer
    {
        /** @var FunctionContainer $result */
        $result = $this->__call('avg', [$distinct]);

        return $result;
    }

    /**
     * @param bool $distinct
     * @return FunctionContainer
     */
    public function max(bool $distinct = false): FunctionContainer
    {
        /** @var FunctionContainer $result */
        $result = $this->__call('max', [$distinct]);

        return $result;
    }

    /**
     * @param bool $distinct
     * @return FunctionContainer
     */
    public function min(bool $distinct = false): FunctionContainer
    {
        /** @var FunctionContainer $result */
        $result = $this->__call('min', [$distinct]);

        return $result;
    }

    public function length(): FunctionContainer
    {
        /** @var FunctionContainer $result */
        $result = $this->__call('length', []);

        return $result;
    }
}
