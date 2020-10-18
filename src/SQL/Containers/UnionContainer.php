<?php namespace EugeneErg\Preparer\SQL\Containers;

use EugeneErg\Preparer\Container;

class UnionContainer extends Container
{
    public function from(QueryContainer $query): UnionContainer
    {
        return $this->__call('from', [$query]);
    }

    public function orderBy(): UnionContainer
    {
        return $this->__call('orderBy', []);
    }
}