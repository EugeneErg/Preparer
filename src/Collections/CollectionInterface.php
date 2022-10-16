<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Collections;

interface CollectionInterface extends \IteratorAggregate, \ArrayAccess
{
    public static function fromMerge(self ...$collections): static;
    public static function fromMap(callable $callback, self ...$collections): static;
    public static function fromArray(array $items): static;
    public static function fromDiff(bool|callable $value, bool|callable $key, self ...$collections): static;
    public function reverse(bool $preserveKeys = false): static;
    public function filter(callable $callback = null): static;
    public function slice(): static;
    public function splice(int $offset = 0, int $length = null, self $replacement = null): static;

    public function isValidItem(mixed $item): bool;
    public function offsetExists($offset): bool;
    public function isEmpty(): bool;

    public function first(): mixed;
    public function last(): mixed;
    public function reduce(callable $callback, mixed $initial = null): mixed;
    public function offsetGet($offset): mixed;
    public function shift(): mixed;

    public function firstKey(): int|string|null;
    public function lastKey(): int|string|null;

    public function getIterator(): iterable;
    public function toArray(): array;

    public function offsetSet($offset, $value): void;
    public function offsetUnset($offset): void;
}
