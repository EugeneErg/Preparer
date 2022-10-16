<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Collections;

class SpecialCollection implements \ArrayAccess, \IteratorAggregate
{
    protected const ITEM_VALUE = null;
    protected const ITEM_KEY = null;

    private function __construct(private ?\iterable $items = null)
    {
        $this->validateItems($items);
    }

    private static function fromIterable(\iterable $items): self
    {
        return new static($items);
    }

    public function isValidValue(mixed $value): bool
    {
        return $this->validateByRule($value, static::ITEM_VALUE);
    }

    public function isValidKey(mixed $key): bool
    {
        return $this->validateByRule($key, static::ITEM_KEY);
    }

    public function offsetExists($offset): bool
    {
        foreach ($this as $key => $value) {
            if ($key === $offset) {
                return $value !== null;
            }
        }

        return false;
    }

    public function offsetGet($offset): mixed
    {

        foreach ($this as $key => $value) {
            if ($key === $offset) {
                return $value !== null;
            }
        }
    }

    public function offsetSet($offset, $value): void
    {
        // TODO: Implement offsetSet() method.
    }

    public function offsetUnset($offset): void
    {
        // TODO: Implement offsetUnset() method.
    }

    public function getIterator(): iterable
    {
        return $this->items ?? [];
    }

    private function validateValue(mixed $item): void
    {
        if (!$this->isValidValue($item)) {
            throw new \InvalidArgumentException('Invalid value');
        }
    }

    private function validateKey(mixed $key): void
    {
        if (!$this->isValidKey($key)) {
            throw new \InvalidArgumentException('Invalid key');
        }
    }

    private function validateItems(?\iterable $items): void
    {
        if ($items === null) {
            return;
        }

        foreach ($items as $key => $item) {
            $this->validateValue($item);
            $this->validateKey($key);
        }
    }

    private function validateByRule(mixed $value, ?string $rule): bool
    {
        if ($rule === null) {
            return true;
        }

        if (is_callable($rule)) {
            return $rule($value);
        }

        return $value instanceof $rule;
    }
}
