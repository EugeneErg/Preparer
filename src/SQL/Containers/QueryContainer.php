<?php namespace EugeneErg\Preparer\SQL\Containers;

use EugeneErg\Preparer\SQL\Field;
use EugeneErg\Preparer\SQL\Table;

class QueryContainer extends SubQueryContainer
{
    public function delete(Table ...$tables): self
    {
        return $this->__call('delete', $tables);
    }

    /**
     * @param Field $field
     * @param string|AggregateFunctionContainer|Field|FunctionContainer $value
     * @return QueryContainer
     */
    public function update(Field $field, $value): self
    {
        return $this->__call('update', [$field, $value]);
    }

    /**
     * @param string $fieldName
     * @param string|AggregateFunctionContainer|Field|FunctionContainer $value
     * @return QueryContainer
     */
    public function select(string $fieldName, $value): self
    {
        return $this->__call('select', [$fieldName, $value]);
    }
}