<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Queries;

use EugeneErg\Preparer\Collections\TypeCollection;
use EugeneErg\Preparer\Enums\JoinTypeEnum;
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
use JetBrains\PhpStorm\Pure;

class SelectQuery extends AbstractQuery
{
    #[Pure] public function __construct(
        public readonly bool $distinct = false,
        public readonly ?int $limit = null,
        public readonly int $offset = 0,
    ) {
        parent::__construct();
    }

    public function count(
        CountableTypeInterface|null $value = null,
        bool $distinct = false,
        ?TypeCollection $partitionBy = null,
        ?TypeCollection $orderBy = null,
    ): AggregateType {
        return $this->call(new Count($value ?? $this, $distinct, $partitionBy, $orderBy));
    }

    public function orderBy(FieldTypeInterface $value, bool $desc = false): self
    {
        return $this->call(new OrderBy($value, $desc));
    }

    public function groupBy(FieldTypeInterface $value): self
    {
        return $this->call(new GroupBy($value));
    }

    public function having(BooleanType $value): self
    {
        return $this->call(new Having($value));
    }

    public function from(QueryTypeInterface $source, JoinTypeEnum $joinType = JoinTypeEnum::Outer): self
    {
        return $this->call(new From($source, $joinType));
    }
}
