<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Functions\Custom;

use EugeneErg\Preparer\Functions\AbstractFunction;
use EugeneErg\Preparer\Types\AbstractType;
use EugeneErg\Preparer\Types\AngleType;
use EugeneErg\Preparer\Types\NumericType;
use EugeneErg\Preparer\Types\MathTypeInterface;
use EugeneErg\Preparer\Types\TypeInterface;

class Plus extends AbstractFunction
{
    public function __construct(public readonly MathTypeInterface $value)
    {
    }

    public function equals(AbstractFunction $function): bool
    {
        return parent::equals($function)
            && $this->value === $function->value;
    }

    /**
     * @param MathTypeInterface $type
     * @return MathTypeInterface
     */
    public function __invoke(): AbstractType
    {
        if (($this->value instanceof AngleType && get_class($this->value) !== get_class($this->context))
            || ($this->value instanceof NumericType && !$this->context instanceof NumericType)
        ) {
            throw new \InvalidArgumentException('All arguments being compared must be of the same type.');
        }

        return parent::__invoke();
    }
}
