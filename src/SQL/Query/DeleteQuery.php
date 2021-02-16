<?php namespace EugeneErg\Preparer\SQL\Query;

use EugeneErg\Preparer\SQL\Table;

class DeleteQuery extends AbstractQuery
{
    private Table $table;

    public function __construct(Table $table, int $limit = null, int $offset = 0)
    {
        parent::__construct($limit, $offset);
        $this->table = $table;
    }

    public function getTable(): Table
    {
        return $this->table;
    }
}
