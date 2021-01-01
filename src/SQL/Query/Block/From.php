<?php namespace EugeneErg\Preparer\SQL\Query\Block;

use EugeneErg\Preparer\SQL\Query\SubQueryInterface;

class From
{
    public const TYPE_CORRELATE = 'correlate';
    public const TYPE_LEFT = 'left';
    public const TYPE_RIGHT = 'right';
    public const TYPE_INNER = 'inner';
    public const TYPE_OUTER = 'outer';

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
