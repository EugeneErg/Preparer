<?php namespace EugeneErg\Preparer\SQL\Query;

class UpdateQuery extends AbstractQuery
{
    private array $values;
    private Table $table;

    public function __construct(Table $table, array $values, int $limit = null, int $offset = 0)
    {
        parent::__construct($limit, $offset);
        $this->values = $values;
        $this->table = $table;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function getTable(): Table
    {
        return $this->table;
    }
}
