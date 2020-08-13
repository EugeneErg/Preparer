<?php namespace EugeneErg\Preparer\Record;

use EugeneErg\Preparer\Action\AbstractAction;
use EugeneErg\Preparer\Action\Method;
use EugeneErg\Preparer\Action\Offset;
use EugeneErg\Preparer\Action\Property;
use EugeneErg\Preparer\ClassCreatorService;
use EugeneErg\Preparer\Container;
use ReflectionException;

abstract class AbstractRecord
{
    protected const ACTIONS = [
        Container::ACTION_OFFSET => Offset::class,
        Container::ACTION_CALL => Method::class,
        Container::ACTION_GET => Property::class,
    ];

    /**
     * @var Container
     */
    private $container;

    /**
     * @var AbstractAction[]
     */
    private $actions = [];

    /**
     * @var ClassCreatorService
     */
    private $classCreatorService;

    public function __construct()
    {
        $this->container = new Container($this);
        $this->classCreatorService = ClassCreatorService::instance();
    }

    /**
     * @return Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * @return AbstractAction[]
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    /**
     * @param AbstractAction[] $actions
     */
    protected function setActions(array $actions): void
    {
        $this->actions = $actions;
    }

    /**
     * @param AbstractAction $action
     * @return Container
     */
    abstract protected function getChildContainer(AbstractAction $action): Container;

    /**
     * @return string
     */
    abstract public function getStringValue(): string;

    /**
     * @param string $actionType
     * @param array $arguments
     * @return Container
     * @throws ReflectionException
     */
    public function getNext(string $actionType, array $arguments): Container
    {
        return $this->getChildContainer($this->createAction($actionType, $arguments));
    }

    /**
     * @param string $actionType
     * @param array $arguments
     * @return AbstractAction
     * @throws ReflectionException
     */
    protected function createAction(string $actionType, array $arguments): AbstractAction
    {
        return $this->classCreatorService->createSingle(self::ACTIONS[$actionType], $arguments);
    }
}