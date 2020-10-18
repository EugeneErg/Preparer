<?php namespace EugeneErg\Preparer\SQL;

class Values extends AbstractSql
{
    /**
     * @var array[]
     */
    private array $values;

    /**
     * Values constructor.
     * @param array[] $values
     * @param bool $distinct
     * @param int|null $limit
     * @param int $offset
     */
    public function __construct(array $values, bool $distinct = false, int $limit = null, $offset = 0)
    {
        $this->values = $values;
        parent::__construct($distinct, $limit, $offset);
    }

    /**
     * @return array[]
     */
    public function getValues(): array
    {
        return $this->values;
    }
}
