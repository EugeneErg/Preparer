<?php namespace EugeneErg\Preparer\SQL\Query;

class SubQuery extends AbstractQuery implements SubQueryInterface
{
    private bool $distinct;

    public function __construct(bool $distinct = false, int $limit = null, int $offset = 0)
    {
        parent::__construct($limit, $offset);
        $this->distinct = $distinct;
    }

    public function isDistinct(): bool
    {
        return $this->distinct;
    }
}
