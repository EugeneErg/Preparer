<?php namespace EugeneErg\Preparer\SQL\Containers;

use EugeneErg\Preparer\SQL\Query\ModelQueryInterface;

class AddedAggregateFunctionContainer extends AbstractAggregateFunctionContainer
{
    private ModelQueryInterface $addedQuery;

    public function __construct(ModelQueryInterface $addedQuery)
    {
        $this->addedQuery = $addedQuery;
    }

    public function count(): self
    {
        return $this->createNewByFunction('count');
    }

    public function getAddedQuery(): ModelQueryInterface
    {
        return $this->addedQuery;
    }
}
