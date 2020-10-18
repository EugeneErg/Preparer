<?php namespace EugeneErg\Preparer\SQL\Containers;

use EugeneErg\Preparer\SQL\Query\MainQueryInterface;

class MainAggregateFunctionContainer extends AbstractAggregateFunctionContainer
{
    private MainQueryInterface $mainQuery;

    public function __construct(MainQueryInterface $mainQuery)
    {
        $this->mainQuery = $mainQuery;
    }

    public function count(ValueInteface $value): self
    {
        return $this->createNewByFunction('count', [$value]);
    }

    public function getMainQuery(): MainQueryInterface
    {
        return $this->mainQuery;
    }
}