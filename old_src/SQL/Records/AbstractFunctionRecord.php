<?php namespace EugeneErg\Preparer\SQL\Records;

use EugeneErg\Preparer\Container;
use EugeneErg\Preparer\Record\AbstractTreeRecord;
use EugeneErg\Preparer\SQL\Containers\FunctionContainer;
use EugeneErg\Preparer\ToStringAsHash;

/**
 * @method FunctionContainer getContainer()
 */
abstract class AbstractFunctionRecord extends AbstractTreeRecord
{
    use ToStringAsHash {
        __toString as getStringValue;
    }

    protected function createContainer(): Container
    {
        return new FunctionContainer($this);
    }
}