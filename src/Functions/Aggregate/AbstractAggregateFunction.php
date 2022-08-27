<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Functions\Aggregate;

use EugeneErg\Preparer\Collections\TypeCollection;
use EugeneErg\Preparer\Functions\AbstractFunction;
use EugeneErg\Preparer\Types\AggregateType;

abstract class AbstractAggregateFunction extends AbstractFunction
{
    protected const RETURN_TYPE = AggregateType::class;

    public readonly TypeCollection $partitionBy;
    public readonly TypeCollection $orderBy;

    public function __construct(
        ?TypeCollection $partitionBy = null,
        ?TypeCollection $orderBy = null,
    ) {
        $this->partitionBy = $partitionBy ?? new TypeCollection();
        $this->orderBy = $orderBy ?? new TypeCollection();
    }

    public function equals(AbstractFunction $function): bool
    {
        return parent::equals($function)
            && $this->partitionBy->items === $function->partitionBy->items
            && $this->orderBy->items === $function->orderBy->items;
    }
}
