<?php namespace EugeneErg\Preparer\SQL\Query;

class UpdateQuery extends AbstractQuery
{
    private array $values;

    public function __construct(array $values, int $limit = null, int $offset = 0)
    {
        parent::__construct($limit, $offset);
        $this->values = $values;
    }

    public function getValues(): array
    {
        return $this->values;
    }
}