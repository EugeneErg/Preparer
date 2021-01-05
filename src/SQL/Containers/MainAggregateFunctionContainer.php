<?php namespace EugeneErg\Preparer\SQL\Containers;

use EugeneErg\Preparer\SQL\Query\AbstractQuery;

class MainAggregateFunctionContainer extends AbstractAggregateFunctionContainer
{
    private AbstractQuery $query;

    public function __construct(AbstractQuery $query)
    {
        $this->query = $query;
    }

    public function count(ValueInteface $value): self
    {
        return $this->createNewByFunction('count', [$value]);
    }

    public function getQuery(): AbstractQuery
    {
        return $this->query;
    }
}