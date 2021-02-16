<?php namespace EugeneErg\Preparer\SQL\Query;

use EugeneErg\Preparer\SQL\Raw\AbstractQueryRaw;
use EugeneErg\Preparer\SQL\Value;

class ReturningQuery
{
    private AbstractSource $source;
    private array $select;

    /**
     * @param AbstractSource|AbstractQueryRaw $source
     * @param array $select
     */
    public function __construct($source, array $select)
    {
        if ($source instanceof AbstractSource) {
            $this->source = $source;
        } elseif ($source instanceof AbstractQueryRaw) {
            $this->source = $source->toSubQuery();
        } else {
            throw new \InvalidArgumentException();
        }

        $this->select = $select;
    }

    public function getSource(): AbstractSource
    {
        return $this->source;
    }

    public function getSelect(): array
    {
        return $this->select;
    }
}

$value = new Value([
    'user_id' => 12
]);

$query = new ReturningQuery((new SelectQuery())
    ->from($table = new Table('rgfthe'))
    ->where("{$table->user} = {$value->user_id}")
    ->orderBy($table->id), [
        'id' => $table->id,
    ]);