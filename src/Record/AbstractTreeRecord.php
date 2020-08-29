<?php namespace EugeneErg\Preparer\Record;

use EugeneErg\Preparer\Action\AbstractAction;
use EugeneErg\Preparer\Container;

abstract class AbstractTreeRecord extends AbstractRecord
{
    /**
     * @var static[]
     */
    private array $children = [];

    protected function getChildContainer(AbstractAction $action): Container
    {
        $index = spl_object_hash($action);

        if (!isset($this->children[$index])) {
            $this->children[$index] = $this->createChild($action);
        }

        return $this->children[$index]->getContainer();
    }

    protected function createChild(AbstractAction $action): AbstractTreeRecord
    {
        $result = $this->createRecord();
        $actions = $this->getActions();
        $actions[] = $action;
        $result->setActions($actions);

        return $result;
    }

    protected function createRecord(): AbstractTreeRecord
    {
        return new static();
    }
}
