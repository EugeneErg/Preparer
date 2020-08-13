<?php namespace EugeneErg\Preparer\Record;

use EugeneErg\Preparer\Action\AbstractAction;
use EugeneErg\Preparer\Container;

abstract class AbstractTreeRecord extends AbstractRecord
{
    /**
     * @var static[]
     */
    private $children = [];

    /**
     * @param AbstractAction $action
     * @return Container
     */
    protected function getChildContainer(AbstractAction $action): Container
    {
        $index = spl_object_hash($action);

        if (!isset($this->children[$index])) {
            $this->children[$index] = new static();
            $actions = $this->getActions();
            $actions[] = $action;
            $this->children[$index]->setActions($actions);
        }

        return $this->children[$index]->getContainer();
    }
}
