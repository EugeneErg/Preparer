<?php namespace EugeneErg\Preparer\Record;

use EugeneErg\Preparer\Action\AbstractAction;
use EugeneErg\Preparer\Container;

abstract class AbstractRecord
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var AbstractAction[]
     */
    private $actions = [];

    public function __construct()
    {
        $this->container = $this->createContainer();
    }

    /**
     * @return Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * @return Container
     */
    private function createContainer(): Container
    {
        return new Container(
            function(AbstractAction $action): Container {
                return $this->getChildContainer($action);
            },
            function(): string {
                if (!isset($this->string)) {
                    $this->string = $this->getStringValue();
                }

                return $this->string;
            }
        );
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
    abstract protected function getStringValue(): string;
}