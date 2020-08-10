<?php namespace EugeneErg\Preparer\Record;

use EugeneErg\Preparer\Action\AbstractAction;
use EugeneErg\Preparer\Action\Method;
use EugeneErg\Preparer\Action\Offset;
use EugeneErg\Preparer\Action\Property;
use Closure;

/**
 * Class TypeValueRecord
 * @package EugeneErg\Preparer\RecordOld
 */
abstract class OldAbstractTypeValueRecord extends OldAbstractValueRecord
{
    /**
     * @var string[]|string|Closure
     */
    protected $properties;

    /**
     * @var string[]|string|Closure
     */
    protected $methods;

    /**
     * @var string[]|string|Closure
     */
    protected $offsets;

    /**
     * OldAbstractTypeValueRecord constructor.
     * @inheritDoc
     */
    public function __construct($value)
    {
        parent::__construct($value);
    }

    /**
     * @param AbstractAction $action
     * @return OldAbstractTypeValueRecord|null
     */
    private function getRecordByAction(AbstractAction $action): ?self
    {
        $actionType = [
            Property::class => $this->properties,
            Method::class => $this->methods,
            Offset::class => $this->offsets,
        ][get_class($action)];

        if (is_array($actionType)) {
            return isset($actionType[$action->getName()]) ? new $actionType[$action->getName()] : null;
        }

        if (is_string($actionType)) {
            return new $actionType($action->getName());
        }

        if ($actionType instanceof Closure) {
            return $actionType($action, $this);
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    protected function createByAction(AbstractAction $action): OldAbstractRecord
    {
        if (!$this->hasValue()) {
            return parent::createByAction($action);
        }

        return $this->getRecordByAction($action) ?? parent::createByAction($action);
    }

    /**
     * @inheritDoc
     */
    protected function hasNextValue(AbstractAction $action): bool
    {
        if (!parent::hasNextValue($action)) {
            return false;
        }

        $actionType = [
            Property::class => $this->properties,
            Method::class => $this->methods,
            Offset::class => $this->offsets,
        ][get_class($action)];

        if (is_array($actionType)) {
            return isset($actionType[$action->getName()]);
        }

        if (is_string($actionType)) {
            return true;
        }

        if ($actionType instanceof Closure) {
            return !!$actionType($action, $this);
        }

        return false;
    }
}
