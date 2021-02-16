<?php namespace EugeneErg\Preparer\SQL\Query;

use EugeneErg\Preparer\SQL\Containers\MainAggregateFunctionContainer;
use EugeneErg\Preparer\SQL\Functions\NotFunction;
use EugeneErg\Preparer\SQL\Query\Block\From;
use EugeneErg\Preparer\SQL\Query\Block\Order;
use EugeneErg\Preparer\SQL\Raw\AbstractQueryRaw;
use EugeneErg\Preparer\SQL\Raw\QueryRaw;
use EugeneErg\Preparer\SQL\ValueInterface;

/**
 * @mixin MainAggregateFunctionContainer
 */
abstract class AbstractQuery extends AbstractSource
{
    /** @var self[] */
    private array $sources;
    private ?int $limit;
    private int $offset;
    /** @var From[] */
    private array $from = [];
    /** @var ValueInterface[] */
    private array $where = [];
    /** @var Order[] */
    private array $orderBy = [];

    public function __construct(int $limit = null, int $offset = 0)
    {
        $this->limit = $limit;
        $this->offset = $offset;
        $this->sources[] = $this;
        parent::__construct();
    }

    /**
     * @param AbstractSource|AbstractQueryRaw $source
     * @param string|null $join
     * @return $this
     */
    public function from($source, string $join = null): self
    {
        $this->from[] = new From(
            $source instanceof AbstractQueryRaw
                ? $source->toSubQuery() : $source,
            $join
        );

        return $this;
    }

    /**
     * @param string|ValueInterface $value
     * @return $this
     */
    public function where($value): self
    {
        $this->where[] = $value;

        return $this;
    }


    /**
     * @param string|ValueInterface $value
     * @param bool $ascDirection
     * @return $this
     */
    public function orderBy($value, bool $ascDirection = true): self
    {
        $this->orderBy[] = new Order(
            $value instanceof ValueInterface ? $value : new QueryRaw((string) $value),
            $ascDirection
        );

        return $this;
    }

    /**
     * @param bool $distinct
     * @param ValueInterface|null $value
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
     * @return Order[]
     */
    public function getOrderBy(): array
    {
        return $this->orderBy;
    }

    /**
     * @return ValueInterface[]|string[]
     */
    public function getWhere(): array
    {
        return $this->where;
    }

    public function __clone()
    {
        $this->sources[] = $this;
        $this->from = [];
        $this->where = [];
        $this->orderBy = [];
    }
}
