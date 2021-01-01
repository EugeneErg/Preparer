<?php namespace EugeneErg\Preparer\SQL\Query;

use EugeneErg\Preparer\SQL\Containers\MainAggregateFunctionContainer;
use EugeneErg\Preparer\SQL\Functions\NotFunction;
use EugeneErg\Preparer\SQL\Functions\Traits\FunctionTrait;
use EugeneErg\Preparer\SQL\Query\Block\From;
use EugeneErg\Preparer\SQL\Query\Block\Order;
use EugeneErg\Preparer\ValueInterface;

/**
 * @mixin MainAggregateFunctionContainer
 */
abstract class AbstractQuery implements MainQueryInterface
{
    use FunctionTrait {
        FunctionTrait::__construct as private functionConstructor;
        FunctionTrait::getQuery as private;
    }

    private ?int $limit;
    private int $offset;
    /**
     * @var From[]
     */
    private array $from = [];
    /**
     * @var ValueInterface[]
     */
    private array $where = [];
    /**
     * @var ValueInterface[]
     */
    private array $groupBy = [];
    /**
     * @var Order[]
     */
    private array $orderBy = [];

    public function __construct(int $limit = null, int $offset = 0)
    {
        $this->limit = $limit;
        $this->offset = $offset;
        $this->functionConstructor($this);
    }

    public function from(SubQueryInterface $subQuery, string $join = null): self
    {
        $this->from[] = new From($subQuery, $join);

        return $this;
    }

    public function where(ValueInterface $value): self
    {
        $this->where[] = $value;

        return $this;
    }

    public function groupBy(ValueInterface $value): self
    {
        $this->groupBy[] = $value;

        return $this;
    }

    public function orderBy(ValueInterface $value, bool $ascDirection = true): self
    {
        $this->orderBy[] = new Order($value, $ascDirection);

        return $this;
    }

    /**
     * @param bool $distinct
     * @param ValueInterface|QueryInterface|null $value
     * @return NotFunction
     */
    public function count(bool $distinct = false, $value = null): NotFunction
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

    /**
     * @return From[]
     */
    public function getFrom(): array
    {
        return $this->from;
    }

    /**
     * @return ValueInterface[]
     */
    public function getGroupBy(): array
    {
        return $this->groupBy;
    }

    /**
     * @return Order[]
     */
    public function getOrderBy(): array
    {
        return $this->orderBy;
    }

    /**
     * @return ValueInterface[]
     */
    public function getWhere(): array
    {
        return $this->where;
    }
}
