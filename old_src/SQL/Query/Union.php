<?php namespace EugeneErg\Preparer\SQL\Query;

class Union extends AbstractModel
{
    private bool $all;
    /* @var ReturningQuery[] */
    private array $unions;
    private self $source;

    public function __construct(bool $all, ReturningQuery ...$unions)
    {
        $this->all = $all;
        $this->unions = $unions;
        $this->source = $this;
        parent::__construct();
    }

    public function isAll(): bool
    {
        return $this->all;
    }

    /**
     * @return ReturningQuery[]
     */
    public function getUnions(): array
    {
        return $this->unions;
    }
}
