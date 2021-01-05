<?php namespace EugeneErg\Preparer\SQL\Query\Block;

use EugeneErg\Preparer\SQL\Query\AbstractSource;

class From
{
    public const TYPE_CORRELATE = 'correlate';
    public const TYPE_LEFT = 'left';
    public const TYPE_RIGHT = 'right';
    public const TYPE_INNER = 'inner';
    public const TYPE_OUTER = 'outer';

    private AbstractSource $data;
    private ?string $type;

    public function __construct(AbstractSource $data, string $type = null)
    {
        $this->data = $data;
        $this->type = $type;
    }

    public function getData(): AbstractSource
    {
        return $this->data;
    }

    public function getType(): ?string
    {
        return $this->type;
    }
}
