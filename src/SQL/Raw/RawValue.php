<?php namespace EugeneErg\Preparer\SQL\Raw;

use EugeneErg\Preparer\ValueInterface;

class RawValue implements ValueInterface
{
    private array $items;

    /**
     * RawValue constructor.
     * @param string|int|bool|float| ...$items
     */
    public function __construct(...$items)
    {
        $this->items = $items;
    }

    public function getItems(): array
    {
        return $this->items;
    }
}
