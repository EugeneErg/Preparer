<?php namespace EugeneErg\Preparer\SQL\Records;

use EugeneErg\Preparer\SQL\Containers\QueryContainer;

/**
 * @method QueryContainer getContainer()
 */
class QueryRecord extends AbstractStructureRecord
{
    public function createContainer(): QueryContainer
    {
        return new QueryContainer($this);
    }
}