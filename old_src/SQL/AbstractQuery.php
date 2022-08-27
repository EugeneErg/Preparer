<?php namespace EugeneErg\Preparer\SQL;

use EugeneErg\Preparer\SQL\Containers\AggregateFunctionContainer;
use EugeneErg\Preparer\SQL\Records\AggregateFunctionRecord;

/**
 * @mixin AggregateFunctionContainer
 */
abstract class AbstractQuery
{
    private array $from = [];
    private array $where = [];
    private array $groupBy = [];
    private array $orderBy = [];
    private AggregateFunctionRecord $aggregateFunctionRecord;

    public function __construct(
        public readonly bool $distinct = false,
        public readonly ?int $limit = null,
        public readonly int $offset = 0,
    ) {
        $this->aggregateFunctionRecord = new AggregateFunctionRecord($this);
    }

    public function from(SubQueryInterface $subQuery, string $join = null): self
    {
        $this->from[] = [$subQuery, $join];

        return $this;
    }

    public function where($value): self
    {
        $this->where[] = $value;

        return $this;
    }

    public function groupBy($value): self
    {
        $this->groupBy[] = $value;

        return $this;
    }

    public function orderBy($value, bool $ascDirection= true): self
    {
        $this->orderBy[] = [$value, $ascDirection];

        return $this;
    }

    public function __call(string $name, array $arguments): AggregateFunctionContainer
    {
        return $this->aggregateFunctionRecord->getContainer()->$name(...$arguments);
    }

    public function getFrom(): array
    {
        return $this->from;
    }

    public function getGroupBy(): array
    {
        return $this->groupBy;
    }

    public function getOrderBy(): array
    {
        return $this->orderBy;
    }

    public function getWhere(): array
    {
        return $this->where;
    }
}
