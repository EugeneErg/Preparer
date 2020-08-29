<?php namespace EugeneErg\Preparer\SQL\Records;

use EugeneErg\Preparer\SQL\Containers\FunctionContainer;

/**
 * @method FunctionContainer getContainer()
 */
class ValuesRecord extends AbstractStructureRecord
{
    public function createContainer(): FunctionContainer
    {
        return new FunctionContainer($this);
    }
}
