<?php namespace EugeneErg\Preparer\SQL;

use EugeneErg\Preparer\SQL\Containers\AggregateFunctionContainer;
use EugeneErg\Preparer\SQL\Records\QueryRecord;
use EugeneErg\Preparer\SQL\Records\SubQueryRecord;

/**
 * @mixin AggregateFunctionContainer|SubQueryRecord
 */
class Query extends AbstractQuery
{
    public function __construct()
    {
        parent::__construct(new QueryRecord($this));
    }
}
