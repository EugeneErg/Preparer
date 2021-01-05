<?php namespace EugeneErg\Preparer\SQL\Query;

class Values extends AbstractModel
{
    private array $values;
    private self $source;

    public function __construct(array ...$values)
    {
        parent::__construct();
        $this->values = $values;
        $this->source = $this;
    }

    public function getValues(): array
    {
        return $this->values;
    }
}