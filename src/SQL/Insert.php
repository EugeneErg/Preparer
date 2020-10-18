<?php namespace EugeneErg\Preparer\SQL;

class Insert extends AbstractSql
{
    private array $inserts;

    public function __construct(array $values, bool $distinct = false, int $limit = null, $offset = 0)
    {
        $this->inserts = $values;
        parent::__construct($distinct, $limit, $offset);
    }

    public function getInserts(): array
    {
        return $this->inserts;
    }
}