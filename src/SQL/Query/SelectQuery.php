<?php namespace EugeneErg\Preparer\SQL\Query;

class SelectQuery extends AbstractQuery implements SelectQueryInterface
{
    private array $values;

    public function __construct(array $values, bool $distinct = false, int $limit = null, int $offset = 0)
    {
        parent::__construct($distinct, $limit, $offset);
        $this->values = $values;
    }

    public function getValues(): array
    {
        return $this->values;
    }
}
