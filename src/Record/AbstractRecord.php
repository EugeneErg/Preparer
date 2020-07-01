<?php namespace EugeneErg\Preparer\Record;

use EugeneErg\Preparer\Action\AbstractAction;
use EugeneErg\Preparer\Container;

/**
 * Class AbstractRecord
 * @package EugeneErg\Preparer\Record
 */
abstract class AbstractRecord
{
    /**
     * @var array
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
     * AbstractRecord constructor.
     */
    public function __construct()
    {
        $this->container = new Container(
            function($action) {
                return $this->createChildren($action)
                    ->getContainer();
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
     * @return AbstractAction
     */
    public function getAction(): AbstractAction
    {
        return $this->action;
    }

    /**
     * Rules constructor.
     * @param AbstractAction $action
     * @return $this
     */
    private function createChildren(AbstractAction $action): self
    {
        $this->children[] = $result = $this->createByAction($action);
        $result->path = $this->path;
        $result->path[] = [$this];
        $result->action = $action;

        return $result;
    }

    /**
     * @param AbstractAction $action
     * @return AbstractRecord
     */
    protected function createByAction(AbstractAction $action): self
    {
        return new static();
    }
}
