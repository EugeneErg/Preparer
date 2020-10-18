<?php namespace EugeneErg\Preparer\SQL\Query;

class ValuesQuery extends AbstractModelQuery implements SubQueryInterface, SelectQueryInterface
{
    private array $values;

    public function __construct(array ...$values)
    {
        parent::__construct();
        $this->values = $values;
    }

    public function getValues(): array
    {
        return $this->values;
    }
}