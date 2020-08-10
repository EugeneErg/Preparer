<?php namespace EugeneErg\Preparer\Record;

use EugeneErg\Preparer\Action\AbstractAction;
use EugeneErg\Preparer\Container;

/**
 * Class OnceHashRecordOld
 * @package EugeneErg\Preparer\RecordOld
 */
class OnceHashRecordOld extends HashRecordOld
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