<?php namespace EugeneErg\Preparer\SQL\Query;

use EugeneErg\Preparer\SQL\Query\Block\From;
use EugeneErg\Preparer\SQL\Query\Block\Order;

class Query
{
    private bool $distinct;
    private ?int $limit;
    private int $offset;
    private array $from = [];
    private array $where = [];
    private array $groupBy = [];
    private array $orderBy = [];

    public function __construct(bool $distinct = false, int $limit = null, int $offset = 0)
    {
        $this->distinct = $distinct;
        $this->limit = $limit;
        $this->offset = $offset;
    }

    public function from(SubQueryInterface $query, string $type = null): self
    {
        $this->from[] = new From($query, $type);

        return $this;
    }

    public function where($condition): self
    {
        $this->where[] = $condition;

        return $this;
    }

    public function groupBy($value): self
    {
        $this->groupBy[] = $value;

        return $this;
    }

    public function orderBy($value, bool $isAsc = true): self
    {
        $this->orderBy[] = new Order($value, $isAsc);

        return $this;
    }

    public function insert(array $values, $condition = null): InsertQuery
    {
        return new InsertQuery($this, $values, $condition);
    }

    public function delete(array $tales, $condition = null): DeleteQuery
    {
        return new DeleteQuery($this, $tales, $condition);
    }

    public function select(array $values, $condition = null): SelectQuery
    {
        return new SelectQuery($this, $values, $condition);
    }

    public function update(array $values, $condition): UpdateQuery
    {
        return new UpdateQuery($this, $values, $condition);
    }

    public function sub($condition = null)
    {
        return new SubQuery($this, $condition);
    }

    public function isDistinct(): bool
    {
        return $this->distinct;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }
}
