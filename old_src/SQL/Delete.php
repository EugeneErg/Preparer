<?php namespace EugeneErg\Preparer\SQL;

class Delete extends AbstractSql
{
    /**
     * @var Table[]
     */
    private array $deletes;

    /**
     * Delete constructor.
     * @param Table[] $tables
     * @param int|null $limit
     * @param int $offset
     */
    public function __construct(array $tables, int $limit = null, $offset = 0)
    {
        $this->deletes = $tables;
        parent::__construct(false, $limit, $offset);
    }

    public function getDeletes(): array
    {
        return $this->deletes;
    }
}
