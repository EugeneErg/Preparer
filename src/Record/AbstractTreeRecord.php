<?php namespace EugeneErg\Preparer\Record;

use EugeneErg\Preparer\Action\AbstractAction;
use EugeneErg\Preparer\Action\Method;
use EugeneErg\Preparer\Action\Property;
use EugeneErg\Preparer\Container;
use EugeneErg\Preparer\ValueIndexService;

abstract class AbstractTreeRecord extends AbstractRecord
{
    private const ACTION_TYPE_METHOD = 'method';
    private const ACTION_TYPE_PROPERTY = 'property';
    private const ACTION_TYPE_OFFSET = 'offset';

    /**
     * @var static[][]|static[][][]
     */
    private $childrenByTypes = [];

    /**
     * @var ValueIndexService
     */
    private $valueIndexService;

    public function __construct()
    {
        $this->valueIndexService = ValueIndexService::instance();
        parent::__construct();
    }

    /**
     * @param AbstractAction $action
     * @return Container
     */
    protected function getChildContainer(AbstractAction $action): Container
    {
        $actionType = $this->getActionType($action);
        $name = $action->getName();

        if ($action instanceof Method) {
            $index = $this->valueIndexService->getIndex(...$action->getArguments());

            if (!isset($this->childrenByTypes[$actionType][$name][$index])) {
                $this->childrenByTypes[$actionType][$name][$index] = $this->createChildByAction($action);
            }

            return $this->childrenByTypes[$actionType][$name][$index]->getContainer();
        }

        if (!isset($this->childrenByTypes[$actionType][$name])) {
            $this->childrenByTypes[$actionType][$name] = $this->createChildByAction($action);
        }

        return $this->childrenByTypes[$actionType][$name]->getContainer();
    }

    /**
     * @param AbstractAction $action
     * @return string
     */
    private function getActionType(AbstractAction $action): string
    {
        if ($action instanceof Method) {
            return self::ACTION_TYPE_METHOD;
        }

        if ($action instanceof Property) {
            return self::ACTION_TYPE_PROPERTY;
        }

        return self::ACTION_TYPE_OFFSET;
    }

    /**
     * @param AbstractAction $action
     * @return $this
     */
    private function createChildByAction(AbstractAction $action): self
    {
        $child = new static();
        $actions = $this->getActions();
        $actions[] = $action;
        $child->setActions($actions);

        return $child;
    }
}
