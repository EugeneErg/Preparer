<?php namespace EugeneErg\Preparer\SQL;

class Select extends AbstractSql
{
    private array $selects;

    public function __construct(array $values, bool $distinct = false, int $limit = null, $offset = 0)
    {
        $this->selects = $values;
        parent::__construct($distinct, $limit, $offset);
    }

    public function getSelects(): array
    {
        return $this->selects;
    }
}