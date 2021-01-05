<?php namespace EugeneErg\Preparer\SQL\Containers;

use EugeneErg\Preparer\SQL\Query\AbstractModel;

class AddedAggregateFunctionContainer extends AbstractAggregateFunctionContainer
{
    private AbstractModel $model;

    public function __construct(AbstractModel $model)
    {
        $this->model = $model;
    }

    public function count(): self
    {
        return $this->createNewByFunction('count');
    }

    public function getModel(): AbstractModel
    {
        return $this->model;
    }
}
