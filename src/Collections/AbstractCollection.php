<?php declare(strict_types=1);

namespace EugeneErg\Preparer\Collections;

use JetBrains\PhpStorm\Pure;

class AbstractCollection implements CollectionInterface
{
    protected const ITEM_TYPE = null;

    public function __construct(private array $items = [])
    {
        $this->validateItems($items);
    }

    public static function fromArray(array $items): static
    {
        return new static($items);
    }

    public static function fromMerge(CollectionInterface ...$collections): static
    {
        return static::fromArray(...array_merge(self::collectionsToArrays($collections)));
    }

    public static function fromMap(callable $callback, CollectionInterface ...$collections): static
    {
        return static::fromArray(array_map($callback, ...static::collectionsToArrays($collections)));
    }

    public static function fromDiff(bool|callable $value, bool|callable $key, CollectionInterface ...$collections): static
    {
        $arguments = self::collectionsToArrays($collections);

        if ($value === true) {
            $value = fn(mixed $valueA, mixed $valueB): int => $valueA <=> $valueB;
        }

        if (is_callable($value)) {
            $arguments[] = $value;
        }

        if (is_callable($key)) {
            $arguments[] = $key;
        }

        $method = implode('_', [
            'array',
            (is_callable($value) ? 'u' : '') . 'diff',
            (is_callable($key) ? 'u' : '') . ($key === false ? '' : ($value === false ? 'key' : 'assoc')),
        ]);

        return static::fromArray($method(...$arguments));
    }

    public function isValidItem(mixed $item): bool
    {
        if (static::ITEM_TYPE === null) {
            return true;
        }

        if (is_callable(static::ITEM_TYPE)) {
            return (static::ITEM_TYPE)($item);
        }

        $type = static::ITEM_TYPE;

        return $item instanceof $type;
    }

    public function reduce(callable $callback, mixed $initial = null): mixed
    {
        return array_reduce($this->items, $callback, $initial);
    }

    public function firstKey(): int|string|null
    {
        return array_key_first($this->items);
    }

    public function lastKey(): int|string|null
    {
        return array_key_last($this->items);
    }

    #[Pure] public function first(): mixed
    {
        $key = $this->firstKey();

        return $key === null ? null : $this->items[$key];
    }

    #[Pure] public function last(): mixed
    {
        $key = $this->lastKey();

        return $key === null ? null : $this->items[$key];
    }

    public function getIterator(): iterable
    {
        return new \ArrayIterator($this->items);
    }

    public function toArray(): array
    {
        return $this->items;
    }

    public function offsetExists($offset): bool
    {
        return isset($this->items[(string) $offset]);
    }

    public function offsetGet($offset): mixed
    {
        return $this->items[(string) $offset];
    }

    public function offsetSet($offset, $value): void
    {
        $this->validate($value);
        $offset === null
            ? $this->items[] = $value
            : $this->items[(string) $offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->items[(string) $offset]);
    }

    public function isEmpty(): bool
    {
        return count($this->items) === 0;
    }

    public function reverse(bool $preserveKeys = false): static
    {
        $this->items = array_reverse($this->items, $preserveKeys);

        return $this;
    }

    public function filter(callable $callback = null): static
    {
        $this->items = array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH);

        return $this;
    }

    public function shift(): mixed
    {
        return array_shift($this->items);
    }

    public function splice(int $offset = 0, int $length = null, CollectionInterface $replacement = null): static
    {
        $items = $replacement?->items ?? [];
        $this->validateItems($items);
        $result = array_splice($this->items, $offset, $length, $items);

        return self::fromArray($result);
    }

    public function slice(int $offset = 0, ?int $length = null, bool $preserveKeys = false): static
    {
        $this->items = array_slice($this->items, $offset, $length, $preserveKeys);

        return $this;
    }

    private static function collectionsToArrays(array $collections): array
    {
        return array_map(fn (self $collection): array => $collection->items, $collections);
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
}
