<?php namespace EugeneErg\Preparer\SQL\Containers;

use EugeneErg\Preparer\Container;
use EugeneErg\Preparer\SQL\Field;
use EugeneErg\Preparer\SQL\Table;
use EugeneErg\Preparer\SQL\Value;
use EugeneErg\Preparer\SQL\Values;

class TableContainer extends Container
{
    /**
     * @param string $fieldName
     * @param Value|Field|string|FunctionContainer|AggregateFunctionContainer $value
     * @return $this
     */
    public function insert(string $fieldName, $value): self
    {
        return $this->__call('insert', [$fieldName, $value]);
    }

    /**
     * @param string|null $type
     * @param string|Table|Values|SubQueryContainer $query
     * @param int|null $limit
     * @param int $offset
     * @param bool $distinct
     * @return TableContainer
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
     * @return TableContainer
     */
    public function where($value): self
    {
        return $this->__call('where', [$value]);
    }
}
