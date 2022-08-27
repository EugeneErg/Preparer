<?php namespace EugeneErg\Preparer\Action;

use EugeneErg\Preparer\AbstractVirtualType;

/**
 * Class Offset
 * @package EugeneErg\Preparer\Action
 */
class Offset extends AbstractAction
{
    /**
     * @inheritDoc
     */
    public function run($value)
    {
        return $value[$this->getName()];
    }

    /** @inheritDoc */
    public function has($value): bool
    {
        if (is_array($value)) {
            return array_key_exists($this->getName(), $value);
        }

        return isset($value[$this->getName()]);
    }

    /**
     * @inheritDoc
     */
    public function getResultType(AbstractVirtualType $value): ?string
    {
        $name = $this->getName();

        return $this->getMethodResultType(
            $value,
            is_numeric($name) ? 'getOffsetNumber' : 'get' . $name . 'Offset'
        );
    }
}
