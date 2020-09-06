<?php namespace EugeneErg\Preparer\SQL\Query;

use EugeneErg\Preparer\SQL\Table;

class TableQuery extends AbstractQuery
{
    private const ACTION_SELECT = 'select';

    public function __construct(Table $table, string $action = null)
    {
        parent::__construct($action, );
    }
}
