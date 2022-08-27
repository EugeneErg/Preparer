<?php namespace EugeneErg\Preparer\SQL\Query;

class Values extends AbstractModel
{
    public readonly array $values;
    private readonly self $source;

    public function __construct(array ...$values)
    {
        parent::__construct();
        $this->values = $values;
        $this->source = $this;
    }
}