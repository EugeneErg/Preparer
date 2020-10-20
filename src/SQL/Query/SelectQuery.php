<?php namespace EugeneErg\Preparer\SQL\Query;

class SelectQuery extends AbstractQuery implements SelectQueryInterface
{
    private array $values;
    private bool $distinct;

    public function __construct(array $values, bool $distinct = false, int $limit = null, int $offset = 0)
    {
        parent::__construct($limit, $offset);
        $this->values = $values;
        $this->distinct = $distinct;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function isDistinct(): bool
    {
        return $this->distinct;
    }
}
