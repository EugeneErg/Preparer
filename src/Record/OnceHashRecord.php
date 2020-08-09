<?php namespace EugeneErg\Preparer\Record;

use EugeneErg\Preparer\Action\AbstractAction;
use EugeneErg\Preparer\Container;

/**
 * Class OnceHashRecord
 * @package EugeneErg\Preparer\Record
 */
class OnceHashRecord extends HashRecord
{
    private $actions = [];

    /**
     * @param AbstractAction $action
     * @return Container
     */
    protected function getChildContainer(AbstractAction $action): Container
    {
        $this->actions[] = $action;

        return $this->createContainer();
    }
}