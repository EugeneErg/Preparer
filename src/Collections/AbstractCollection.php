<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Collections;

use EugeneErg\Preparer\DataTransferObjects\Query;

abstract class AbstractCollection
{
    protected const ITEM_TYPE = null;

    /** @param Query[] $items */
    public function __construct(public readonly array $items = [])
    {
        $this->validateItems($items);
    }

    public function isValidItem(mixed $item): bool
    {
        if (self::ITEM_TYPE === null) {
            return true;
        }

        if (is_callable(self::ITEM_TYPE)) {
            return (self::ITEM_TYPE)($item);
        }

        return $item instanceof self::ITEM_TYPE;
    }

    private function validate(mixed $item): void
    {
        if (!$this->isValidItem($item)) {
            throw new \InvalidArgumentException('Invalid item');
        }
    }

    private function validateItems(array $items): void
    {
        foreach ($items as $item) {
            $this->validate($item);
        }
    }

    public function reduce(callable $callback, mixed $initial = null): mixed
    {
        return array_reduce($this->items, $callback, $initial);
    }
}
