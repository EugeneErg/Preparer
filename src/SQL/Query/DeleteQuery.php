<?php namespace EugeneErg\Preparer\SQL\Query;

use EugeneErg\Preparer\SQL\Table;

class DeleteQuery extends AbstractQuery
{
    /**
     * @var Table[]
     */
    private array $tables;

    public function __construct(Table ...$tables)
    {
        parent::__construct($query, $condition);
        $this->tables = $tables;
    }

    /**
     * @return Table[]
     */
    public function getTables(): array
    {
        return $this->tables;
    }
}
