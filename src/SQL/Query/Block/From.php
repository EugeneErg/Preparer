<?php namespace EugeneErg\Preparer\SQL\Query\Block;

use EugeneErg\Preparer\SQL\Query\SubQueryInterface;

class From
{
    private SubQueryInterface $query;
    private ?string $type;

    public function __construct(SubQueryInterface $query, string $type = null)
    {
        $this->query = $query;
        $this->type = $type;
    }

    public function getQuery(): SubQueryInterface
    {
        return $this->query;
    }

    public function getType(): ?string
    {
        return $this->type;
    }
}
