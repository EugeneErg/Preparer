<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Queries;

use EugeneErg\Preparer\Collections\FunctionCollection;
use EugeneErg\Preparer\Enums\QueryTypeEnum;
use EugeneErg\Preparer\Functions\AbstractFunction;
use EugeneErg\Preparer\Functions\Query\Context;
use EugeneErg\Preparer\Functions\Query\Where;
use EugeneErg\Preparer\Types\AbstractType;
use EugeneErg\Preparer\Types\AggregateType;
use EugeneErg\Preparer\Types\BooleanType;
use EugeneErg\Preparer\Types\CountableTypeInterface;
use EugeneErg\Preparer\Types\QueryTypeInterface;
use EugeneErg\Preparer\Types\TypeInterface;

abstract class AbstractQuery extends AbstractType implements CountableTypeInterface, QueryTypeInterface
{
    public function __construct(public readonly QueryTypeEnum $type)
    {
        parent::__construct(new FunctionCollection([(new Context($this))($this)]));
    }

    public function where(BooleanType $value): self
    {
        $this->call(new Where($value));

        return $this;
    }

    /** @return AggregateType|static */
    protected function call(AbstractFunction $function): TypeInterface
    {
        return parent::call($function);
    }
}
