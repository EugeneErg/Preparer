<?php namespace EugeneErg\Preparer\Record;

use EugeneErg\Preparer\Action\AbstractAction;
use EugeneErg\Preparer\Action\Method;
use EugeneErg\Preparer\Action\Offset;
use EugeneErg\Preparer\Action\Property;
use EugeneErg\Preparer\ClassCreatorService;
use EugeneErg\Preparer\Container;
use EugeneErg\Preparer\Exception\InvalidActionException;
use ReflectionException;

abstract class AbstractRecord
{
    protected const ACTIONS = [
        Container::ACTION_OFFSET => Offset::class,
        Container::ACTION_CALL => Method::class,
        Container::ACTION_GET => Property::class,
    ];

    private Container $container;
    /**
     * @var AbstractAction[]
     */
    private array $actions = [];
    private ClassCreatorService $classCreatorService;

    public function __construct()
    {
        $this->container = $this->createContainer();
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

    abstract protected function getChildContainer(AbstractAction $action): Container;
    abstract public function getStringValue(): string;

    /**
     * @param string $actionType
     * @param array $arguments
     * @return Container
     * @throws InvalidActionException
     * @throws ReflectionException
     */
    public function getNext(string $actionType, array $arguments): Container
    {
        if (!$this->validate($actionType, $arguments)) {
            throw new InvalidActionException([
                $actionType,
                $arguments
            ]);
        }

        return $this->getChildContainer($this->createAction($actionType, $arguments));
    }

    private function validate(string $actionType, array $arguments): bool
    {
        if (method_exists($this, "{$actionType}Validate")) {
            return $this->{"{$actionType}Validate"}(...$arguments);
        }

        return isset(static::ACTIONS[$actionType]);
    }

    /**
     * @param string $actionType
     * @param array $arguments
     * @return AbstractAction
     * @throws ReflectionException
     */
    protected function createAction(string $actionType, array $arguments): AbstractAction
    {
        /** @var AbstractAction $result */
        $result = $this->classCreatorService->createSingle(self::ACTIONS[$actionType], $arguments);

        return $result;
    }

    protected function createContainer(): Container
    {
        return new Container($this);
    }
}