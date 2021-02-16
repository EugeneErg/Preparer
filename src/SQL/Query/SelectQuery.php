<?php namespace EugeneErg\Preparer\SQL\Query;

use EugeneErg\Preparer\SQL\ValueInterface;

class SelectQuery extends AbstractQuery
{
    private bool $distinct;
    /* @var string[]|ValueInterface[] */
    private array $groupBy = [];

    public function __construct(bool $distinct = false, int $limit = null, int $offset = 0)
    {
        parent::__construct($limit, $offset);
        $this->distinct = $distinct;
    }

    public function isDistinct(): bool
    {
        return $this->distinct;
    }

    /**
     * @param string|ValueInterface $value
     * @return $this
     */
    public function groupBy($value): self
    {
        $this->groupBy[] = $value;

        return $this;
    }

    /**
     * @return string[]|ValueInterface[]
     */
    public function getGroupBy(): array
    {
        return $this->groupBy;
    }

    public function __clone()
    {
        parent::__clone();
        $this->groupBy = [];
    }
}
