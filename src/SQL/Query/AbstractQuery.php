<?php namespace EugeneErg\Preparer\SQL\Query;

use EugeneErg\Preparer\SQL\Containers\MainAggregateFunctionContainer;
use EugeneErg\Preparer\SQL\Functions\NotFunction;
use EugeneErg\Preparer\SQL\Functions\Traits\FunctionTrait;
use EugeneErg\Preparer\SQL\Query\Block\From;
use EugeneErg\Preparer\SQL\Query\Block\Order;

/**
 * @mixin MainAggregateFunctionContainer
 */
abstract class AbstractQuery implements MainQueryInterface
{
    use FunctionTrait {
        FunctionTrait::__construct as private functionConstructor;
        FunctionTrait::getQuery as private;
    }

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
        $this->functionConstructor($this);
    }

    public function from(SubQueryInterface $subQuery, string $join = null): self
    {
        $this->from[] = new From($subQuery, $join);

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

    public function orderBy($value, bool $ascDirection = true): self
    {
        $this->orderBy[] = new Order($value, $ascDirection);

        return $this;
    }

    public function count(bool $distinct = false, ValueInterface $value = null): NotFunction
    {
        /** @var NotFunction $result */
        $result = $this->call('count', [$distinct, $value]);

        return $result;
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
