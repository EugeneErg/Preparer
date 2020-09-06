<?php namespace EugeneErg\Preparer\SQL;

use EugeneErg\Preparer\SQL\Query\MainQuery;
use EugeneErg\Preparer\SQL\Query\SubQuery;

abstract class AbstractRaw
{
    public function _construct(string $query)
    {
        $this->query = $query;
    }

    public function toSubQuery(): SubQuery
    {

    }

    public function toQuery(): MainQuery
    {

    }

    public function toValue()
    {

    }
}