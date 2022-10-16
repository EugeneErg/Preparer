<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Collections;

use LogicException;

abstract class AbstractImmutableCollection extends AbstractCollection
{
    public function set(mixed $value, mixed $key = null): static
    {
        return $this->clone()->parentSet($value, $key);
    }

    public function unset(mixed $key): static
    {
        return $this->clone()->parentUnset($key);
    }

    public function reverse(bool $preserveKeys = false): static
    {
        return $this->clone()->reverse($preserveKeys);
    }

    public function filter(callable $callback = null): static
    {
        return $this->clone()->filter($callback);
    }

    public function shift(): mixed
    {
        $this->throw();

        return null;
    }

    public function slice(int $offset = 0, ?int $length = null, bool $preserveKeys = false): static
    {
        return $this->clone()->slice($offset, $length, $preserveKeys);
    }

    public function splice(int $offset = 0, ?int $length = null, CollectionInterface $replacement = null): static
    {
        return $this->clone()->splice($offset, $length, $replacement);
    }

    public function offsetSet($offset, $value): void
    {
        $this->throw();
    }

    public function offsetUnset($offset): void
    {
        $this->throw();
    }

    private function throw(): void
    {
        throw new LogicException('Is immutable collection');
    }

    private function clone(): static
    {
        return clone $this;
    }

    private function parentSet(mixed $value, mixed $key = null): static
    {
        parent::offsetSet($key, $value);

        return $this;
    }

    private function parentUnset(mixed $key): static
    {
        parent::offsetUnset($key);

        return $this;
    }
}
