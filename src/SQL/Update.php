<?php namespace EugeneErg\Preparer\SQL;

class Update extends AbstractSql
{
    public function __construct(array $values, int $limit = null, $offset = 0)
    {
        $this->updates = $values;
    }
}