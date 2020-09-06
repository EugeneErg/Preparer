<?php namespace EugeneErg\Preparer\SQL\Query;

class MainQuery extends SubQuery
{
    private const ACTION_SELECT = 'select';
    private const ACTION_DELETE = 'delete';
    private const ACTION_UPDATE = 'update';

    private string $action;

    public function __construct(
        string $action,
        string $index,
        array $subQueries = [],
        array $conditions = [],
        array $sorts = [],
        array $groups = [],
        int $limit = null,
        int $offset = 0,
        bool $distinct = false
    ) {
        $this->action = $action;
        parent::__construct($index, $subQueries, $conditions, $sorts, $groups, $limit, $offset, $distinct);
    }
}