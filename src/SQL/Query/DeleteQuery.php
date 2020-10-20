<?php namespace EugeneErg\Preparer\SQL\Query;

use EugeneErg\Preparer\SQL\Table;

class DeleteQuery extends AbstractQuery
{
    /**
     * @var Table[]
     */
    private array $tables;

    /**
     * DeleteQuery constructor.
     * @param Table[] $tables
     * @param int|null $limit
     * @param int $offset
     */
    public function __construct(array $tables, int $limit = null, int $offset = 0)
    {
        parent::__construct($limit, $offset);
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
