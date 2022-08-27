<?php namespace EugeneErg\Preparer\SQL\Records;

use EugeneErg\Preparer\SQL\Containers\SubQueryContainer;

/**
 * @method SubQueryContainer getContainer()
 */
class SubQueryRecord extends AbstractStructureRecord
{
    public function createContainer(): SubQueryContainer
    {
        return new SubQueryContainer($this);
    }
}
