<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Functions\Aggregate;

use EugeneErg\Preparer\Collections\TypeCollection;
use EugeneErg\Preparer\Functions\AbstractFunction;
use EugeneErg\Preparer\Types\CountableTypeInterface;
use EugeneErg\Preparer\Types\TypeInterface;

class Count extends AbstractAggregateFunction
{
    public function __construct(
        TypeInterface $context,
        public readonly CountableTypeInterface $value,
        public readonly bool $distinct = false,
        ?TypeCollection $partitionBy = null,
        ?TypeCollection $orderBy = null,
    ) {
        parent::__construct($context, $partitionBy, $orderBy);
    }

    public function equals(AbstractFunction $function): bool
    {
        return parent::equals($function)
            && $this->value === $function->value
            && $this->distinct === $function->distinct;
    }
}
