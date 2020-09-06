<?php namespace EugeneErg\Preparer\SQL\Records;

use EugeneErg\Preparer\Action\AbstractAction;
use EugeneErg\Preparer\Action\Method;
use EugeneErg\Preparer\SQL\Containers\FunctionContainer;

/**
 * @method FunctionContainer getContainer()
 */
class AbstractSubFunctionRecord extends AbstractFunctionRecord
{
    protected function createChild(AbstractAction $action): AbstractFunctionRecord
    {
        /** @var FunctionRecord $result */
        $result = $action instanceof Method
            ? (new FunctionRecord($this))->createChild($action)
            : parent::createChild($action);

        return $result;
    }
}
