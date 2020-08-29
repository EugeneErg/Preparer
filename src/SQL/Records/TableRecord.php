<?php namespace EugeneErg\Preparer\SQL\Records;

use EugeneErg\Preparer\SQL\Containers\TableContainer;

/**
 * @method TableContainer getContainer()
 */
class TableRecord extends AbstractStructureRecord
{
    public function createContainer(): TableContainer
    {
        return new TableContainer($this);
    }
}