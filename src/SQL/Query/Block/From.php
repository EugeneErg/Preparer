<?php namespace EugeneErg\Preparer\SQL\Query\Block;

use EugeneErg\Preparer\SQL\Query\AbstractSource;

class From
{
    public const TYPE_CORRELATE = 'correlate';
    public const TYPE_LEFT = 'left';
    public const TYPE_RIGHT = 'right';
    public const TYPE_INNER = 'inner';
    public const TYPE_OUTER = 'outer';

    private AbstractSource $source;
    private ?string $type;

    public function __construct(AbstractSource $source, string $type = null)
    {
        $this->source = $source;
        $this->type = $type;
    }

    public function getSource(): AbstractSource
    {
        return $this->source;
    }

    public function getType(): ?string
    {
        return $this->type;
    }
}
