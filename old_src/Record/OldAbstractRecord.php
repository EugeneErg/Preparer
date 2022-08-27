<?php namespace EugeneErg\Preparer\Record;

use EugeneErg\Preparer\Action\AbstractAction;
use EugeneErg\Preparer\Container;

/**
 * Class OldAbstractRecord
 * @package EugeneErg\Preparer\RecordOld
 */
abstract class OldAbstractRecord
{
    /**
     * @var self[]
     */
    private $path = [];

    /**
     * @var Container
     */
    private $container;

    /**
     * @var Container[]
     */
    private $children = [];

    /**
     * @var AbstractAction
     */
    private $action;

    /**
     * @var string
     */
    private $string;

    /**
     * @return string
     */
    abstract protected function getStringValue(): string;

    /**
     * OldAbstractRecord constructor.
     */
    public function __construct()
    {
        $this->container = $this->createContainer();
    }

    /**
     * @return self
     */
    public function getRoot(): self
    {
        return $this->path[0] ?? $this;
    }

    /**
     * @return Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * @param int $level
     * @return self|null
     */
    public function getParent(int $level = 1): ?self
    {
        $level = $level <= 0 ? - $level : count($this->path) - $level;

        return $this->path[$level] ?? null;
    }

    /**
     * @return self[]
     */
    public function getPath(): array
    {
        return $this->path;
    }

    /**
     * @return AbstractAction[]
     */
    public function getActions(): array
    {
        $actions = [];

        foreach ($this->path as $parent) {
            $actions[] = $parent->getAction();
        }

        $actions[] = $this->getAction();

        return $actions;
    }

    /**
     * @return AbstractAction
     */
    public function getAction(): AbstractAction
    {
        return $this->action;
    }

    /**
     * @return Container
     */
    protected function createContainer(): Container
    {
        return new Container(
            function($action) {
                return $this->getChildContainer($action);
            },
            function() {
                if (!isset($this->string)) {
                    $this->string = $this->getStringValue();
                }

                return $this->string;
            }
        );
    }

    /**
     * @param AbstractAction $action
     * @return Container
     */
    protected function getChildContainer(AbstractAction $action): Container
    {
        $this->children[] = $result = $this->createByAction($action);
        $result->path = $this->path;
        $result->path[] = [$this];
        $result->action = $action;

        return $result->getContainer();
    }

    /**
     * @param AbstractAction $action
     * @return OldAbstractRecord
     */
    protected function createByAction(AbstractAction $action): self
    {
        return new static();
    }
}
