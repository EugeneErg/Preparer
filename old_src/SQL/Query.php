<?php namespace EugeneErg\Preparer\SQL;

use EugeneErg\Preparer\SQL\Containers\QueryContainer;

class Query
{
    public function insert(QueryContainer $query, array $inserts): string
    {

    }

    public function select(QueryContainer $query, array $selects): string
    {

    }

    public function delete(QueryContainer $query, Table ...$tables): string
    {

    }

    public function update(QueryContainer $query, array $updates): string
    {

    }

    public function rawExecute(string $query)
    {

    }
}