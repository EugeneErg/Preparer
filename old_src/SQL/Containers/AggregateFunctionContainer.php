<?php namespace EugeneErg\Preparer\SQL\Containers;

use EugeneErg\Preparer\Container;
use EugeneErg\Preparer\SQL\Field;

class AggregateFunctionContainer extends Container
{
    /**
     * @param Field|Container|String|null $value
     * @param bool $distinct
     * @return FunctionContainer
     */
    public function count($value = null, bool $distinct = false): FunctionContainer
    {
        /** @var FunctionContainer $result */
        $result = $this->__call('count', [$distinct, $value]);

        return $result;
    }

    public function exists(): FunctionContainer
    {
        /** @var FunctionContainer $result */
        $result = $this->__call('exists', []);

        return $result;
    }

    /**
     * @param Field|Container|string $value
     * @param bool $distinct
     * @return FunctionContainer
     */
    public function sum($value, bool $distinct = false): FunctionContainer
    {
        /** @var FunctionContainer $result */
        $result = $this->__call('sum', [$value, $distinct]);

        return $result;
    }

    /**
     * @param Field|Container|string $value
     * @param bool $distinct
     * @return FunctionContainer
     */
    public function avg($value, bool $distinct = false): FunctionContainer
    {
        /** @var FunctionContainer $result */
        $result = $this->__call('avg', [$value, $distinct]);

        return $result;
    }

    /**
     * @param Field|Container|string $value
     * @param bool $distinct
     * @return FunctionContainer
     */
    public function max($value, bool $distinct = false): FunctionContainer
    {
        /** @var FunctionContainer $result */
        $result = $this->__call('max', [$value, $distinct]);

        return $result;
    }

    /**
     * @param Field|Container|string $value
     * @param bool $distinct
     * @return FunctionContainer
     */
    public function min($value, bool $distinct = false): FunctionContainer
    {
        /** @var FunctionContainer $result */
        $result = $this->__call('min', [$value, $distinct]);

        return $result;
    }
}
