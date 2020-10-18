<?php namespace EugeneErg\Preparer\SQL\Query;

class UnionQuery extends AbstractModelQuery implements SelectQueryInterface, SubQueryInterface
{
    private bool $all;
    /**
     * @var SelectQueryInterface[]
     */
    private array $unions = [];

    public function __construct(bool $all, SelectQueryInterface ...$unions)
    {
        $this->all = $all;
        $this->unions = $unions;
        parent::__construct();
    }

    public function isAll(): bool
    {
        return $this->all;
    }

    /**
     * @return SelectQueryInterface[]
     */
    public function getUnions(): array
    {
        return $this->unions;
    }
}
