<?php namespace EugeneErg\Preparer\SQL\Query;

class SubQuery
{
    private string $index;
    /** @var SubQuery[]  */
    private array $subQueries;
    private array $conditions;
    private array $sorts;
    private array $groups;
    private bool $distinct;
    private ?int $limit;
    private int $offset;

    public function __construct(

        string $index,
        array $subQueries,
        array $conditions,
        array $sorts,
        array $groups,
        int $limit = null,
        int $offset = 0,
        bool $distinct = false
    ) {
        $this->index = $index;
        $this->subQueries = $subQueries;
        $this->conditions = $conditions;
        $this->sorts = $sorts;
        $this->groups = $groups;
        $this->limit = $limit;
        $this->offset = $offset;
        $this->offset = $offset;
        $this->distinct = $distinct;
    }
}