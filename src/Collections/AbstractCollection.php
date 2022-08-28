<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Collections;

abstract class AbstractCollection implements \IteratorAggregate, \ArrayAccess
{
    protected const ITEM_TYPE = null;

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

    public function set(mixed $value, string|int|null $key = null): static
    {
        $result = clone $this;
        $this->validate($value);
        $key === null
            ? $this->items[] = $value
            : $this->items[$key] = $value;

        return $result;
    }

    public function unset(string|int $key): static
    {
        $result = clone $this;
        unset($result->items[$key]);

        return $result;
    }

    public function first(): mixed
    {
        $result = reset($this->items);
        $isValidElement = key($this->items);

        return $isValidElement === null ? null : $result;
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->items);
    }

    public static function fromMerge(self ...$collections): static
    {
        return new static(
            ...array_merge(array_map(fn (self $collection): array => $collection->toArray(), $collections)),
        );
    }

    private function toArray(): array
    {
        return new $this->items;
    }

    public function offsetExists($offset): bool
    {
        return isset($this->items[$offset]);
    }

    public function offsetGet($offset): mixed
    {
        return $this->items[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        throw new \LogicException('Is immutable collection');
    }

    public function offsetUnset($offset): void
    {
        throw new \LogicException('Is immutable collection');
    }

    public function isEmpty(): bool
    {
        return count($this->items) === 0;
    }
}
