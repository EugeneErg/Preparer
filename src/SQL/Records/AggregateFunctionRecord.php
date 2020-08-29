<?php namespace EugeneErg\Preparer\SQL\Records;

use EugeneErg\Preparer\SQL\AbstractQuery;
use EugeneErg\Preparer\SQL\Containers\AggregateFunctionContainer;

/**
 * @method AggregateFunctionContainer getContainer()
 */
class AggregateFunctionRecord extends AbstractFunctionRecord
{
    private AbstractQuery $query;

    public function __construct(AbstractQuery $query)
    {
        $this->query = $query;
        parent::__construct();
    }

    public function getQuery(): AbstractQuery
    {
        return $this->query;
    }

    protected function createRecord(): FunctionRecord
    {
        return new FunctionRecord($this);
    }

    protected function createContainer(): AggregateFunctionContainer
    {
        return new AggregateFunctionContainer($this);
    }
}
