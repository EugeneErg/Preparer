<?php namespace EugeneErg\Preparer\Record;

use EugeneErg\Preparer\Action\AbstractAction;

/**
 * Class ValueRecord
 * @package EugeneErg\Preparer\Record
 */
abstract class AbstractValueRecord extends AbstractRecord
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @var bool
     */
    private $hasValue;

    /**
     * @var AbstractAction[]
     */
    private $actions = [];

    /**
     * ValueRecord constructor.
     * @param mixed $value
     */
    public function __construct($value = null)
    {
        $this->value = $value;
        $this->hasValue = func_num_args() > 0;
        parent::__construct();
    }

    /** @inheritDoc */
    protected function createByAction(AbstractAction $action): AbstractRecord
    {
        if ($this->hasNextValue($action)) {
            return new static($action->run($this->value));
        }

        $result = clone $this;
        $result->hasValue = false;
        $result->value = null;
        $result->actions[] = $action;

        return $result;
    }

    /**
     * @param AbstractAction $action
     * @return bool
     */
    protected function hasNextValue(AbstractAction $action): bool
    {
        return $this->hasValue && $action->has($this->value);
    }

    /**
     * @return bool
     */
    public function hasValue(): bool
    {
        return $this->hasValue;
    }

    /**
     * @return AbstractAction[]
     */
    public function getActions(): array
    {
        return $this->actions;
    }
}
