<?php namespace EugeneErg\Preparer\Record;

use EugeneErg\Preparer\Action\AbstractAction;
use EugeneErg\Preparer\Container;

abstract class AbstractListRecord extends AbstractRecord
{
    /**
     * @param AbstractAction $action
     * @return Container
     */
    protected function getChildContainer(AbstractAction $action): Container
    {
        $actions = $this->getActions();
        $actions[] = $action;
        $this->setActions($actions);

        return $this->getContainer();
    }
}