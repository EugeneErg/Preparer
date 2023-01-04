<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Queries;

use EugeneErg\Preparer\Collections\TypeCollection;
use EugeneErg\Preparer\Enums\JoinTypeEnum;
use EugeneErg\Preparer\Enums\QueryTypeEnum;
use EugeneErg\Preparer\Functions\Aggregate\Count;
use EugeneErg\Preparer\Functions\Query\From;
use EugeneErg\Preparer\Functions\Query\GroupBy;
use EugeneErg\Preparer\Functions\Query\Having;
use EugeneErg\Preparer\Functions\Query\OrderBy;
use EugeneErg\Preparer\Types\AggregateType;
use EugeneErg\Preparer\Types\BooleanType;
use EugeneErg\Preparer\Types\CountableTypeInterface;
use EugeneErg\Preparer\Types\FieldTypeInterface;
use EugeneErg\Preparer\Types\QueryTypeInterface;

class SelectQuery extends AbstractQuery
{
    public function __construct(
        public readonly bool $distinct = false,
        public readonly ?int $limit = null,
        public readonly int $offset = 0,
    ) {
        parent::__construct(QueryTypeEnum::Select);
    }

    public function count(
        CountableTypeInterface|null $value = null,
        bool $distinct = false,
        ?TypeCollection $partitionBy = null,
        ?TypeCollection $orderBy = null,
    ): AggregateType {
        return $this->call(new Count($this, $value ?? $this, $distinct, $partitionBy, $orderBy));
    }

    public function orderBy(FieldTypeInterface $value, bool $desc = false): self
    {
        $this->call(new OrderBy($value, $desc));

        return $this;
    }

    public function groupBy(FieldTypeInterface ...$values): self
    {
        $this->call(new GroupBy(...$values));

        return $this;
    }

    public function having(BooleanType $value): self
    {
        $this->call(new Having($value));

        return $this;
    }

    public function from(
        QueryTypeInterface $source,
        BooleanType $on = null,
        JoinTypeEnum $joinType = JoinTypeEnum::Outer,
    ): self {
        $this->call(new From($source, $on, $joinType));

        return $this;
    }
}
