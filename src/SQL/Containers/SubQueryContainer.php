<?php namespace EugeneErg\Preparer\SQL\Containers;

use EugeneErg\Preparer\Container;
use EugeneErg\Preparer\SQL\AbstractQuery;
use EugeneErg\Preparer\SQL\Field;
use EugeneErg\Preparer\SQL\Query\SubQuery;
use EugeneErg\Preparer\SQL\Table;
use EugeneErg\Preparer\SQL\Values;

class SubQueryContainer extends Container
{
    /**
     * @param string|null $type
     * @param string|Table|Values|SubQueryContainer $query
     * @param int|null $limit
     * @param int $offset
     * @param bool $distinct
     * @return $this
     */
    public function from(
        ?string $type,
        $query,
        int $limit = null,
        int $offset = 0,
        bool $distinct = false
    ): self {
        return $this->__call('from', [$type, $query, $limit, $offset, $distinct]);
    }

    /**
     * @param string|AggregateFunctionContainer|Field|FunctionContainer $value
     * @return $this
     */
    public function where($value): self
    {
        return $this->__call('where', [$value]);
    }

    /**
     * @param string|AggregateFunctionContainer|Field|FunctionContainer $value
     * @param bool $directionAsc
     * @return $this
     */
    public function orderBy($value, bool $directionAsc = true): self
    {
        return $this->__call('orderBy', [$value, $directionAsc]);
    }

    /**
     * @param string|AggregateFunctionContainer|Field|FunctionContainer $value
     * @return $this
     */
    public function groupBy($value): self
    {
        return $this->__call('groupBy', [$value]);
    }
}
