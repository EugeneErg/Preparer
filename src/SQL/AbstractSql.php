<?php namespace EugeneErg\Preparer\SQL;

use EugeneErg\Preparer\SQL\Containers\AggregateFunctionContainer;
use EugeneErg\Preparer\SQL\Records\AggregateFunctionRecord;

abstract class AbstractSql
{
    private bool $distinct;
    private ?int $limit;
    private int $offset;
    private array $from = [];
    private array $where = [];
    private array $groupBy = [];
    private array $orderBy = [];
    private AggregateFunctionRecord $aggregateFunctionRecord;

    public function __construct(bool $distinct = false, int $limit = null, int $offset = 0)
    {
        $this->distinct = $distinct;
        $this->limit = $limit;
        $this->offset = $offset;
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

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function isDistinct(): bool
    {
        return $this->distinct;
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
