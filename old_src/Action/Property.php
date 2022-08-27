<?php namespace EugeneErg\Preparer\Action;

use EugeneErg\Preparer\AbstractVirtualType;

/**
 * Class Property
 * @package EugeneErg\Preparer\Action
 */
class Property extends AbstractAction
{
    /**
     * @inheritDoc
     */
    public function run($value)
    {
        return $value->{$this->getName()};
    }

    /**
     * @inheritDoc
     */
    public function has($value): bool
    {
        return property_exists($value, $this->getName());
    }

    /**
     * @inheritDoc
     */
    public function getResultType(AbstractVirtualType $value): ?string
    {
        return $this->getMethodResultType(
            $value,
            'get' . $this->getName() . 'Attribute'
        );
    }
}
