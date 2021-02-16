<?php namespace EugeneErg\Preparer;

class Collection implements \Iterator
{
    private array $items;

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public function map(\Closure $callback, self ...$collections): self
    {
        $arrays = [];

        foreach ($collections as $collection) {
            $arrays[] = $collection->items;
        }

        return new self(array_map($callback, $this->items, ...$arrays));
    }

    public function group(\Closure $callback): self
    {
        $groups = [];

        foreach ($this->items as $item => $number) {
            $group = $callback($item, $number);

            if ($group !== null) {
                $groups[$group][] = $item;
            }
        }

        $result = [];

        foreach ($groups as $name => $group) {
            $result[$name] = new self($group);
        }

        return new self($result);
    }

    public function explode(\Closure $callback): self
    {
        $index = 0;

        return $this->group(static function($item, $key) use($callback, &$index): ?int {
            $result = $callback($item, $key);

            if ($result === true) {
                $index++;
            }

            return $result ? null : $index;
        });
    }

    public function search(\Closure $callback): ?string
    {
        foreach ($this->items as $item => $position) {
            $position = (string) $position;
            $result = $callback($item, $position);

            if (is_bool($result)) {
                return $result ? $position : null;
            }
        }

        return null;
    }

    public function filter(\Closure $callback): self
    {
        $result = $this->group(static function($item, $key) use($callback): ?int {
            return $callback($item, $key) ? 0 : null;
        });

        return $result->items[0] ?? new self();
    }

    public function reverse(bool $preserveKeys = false): self
    {
        return new self(array_reverse($this->items, $preserveKeys));
    }

    public function values(): self
    {
        return new self(array_values($this->items));
    }

    public function toArray(): array
    {
        return $this->items;
    }

    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @param \Closure $callback
     * @param mixed $initial
     * @return mixed
     */
    public function reduce(\Closure $callback, $initial = null)
    {
        foreach ($this->items as $key => $item) {
            $initial = $callback($initial, $item, $key);
        }

        return $initial;
    }

    /**
     * @param \Closure $callback
     * @param \Closure|null $createBranch
     * @return $this
     * @throws \Exception
     */
    public function tree(\Closure $callback, \Closure $createBranch = null): self
    {
        $result = new self();
        $level = 0;
        $branches = [$level => $result];

        foreach ($this->items as $key => $item) {
            $value = $callback($item, $key);

            if ($value === true) {
                $branches[++$level] = new self();
            } elseif ($value === false) {
                $branches[$level][] = $item;
                $branches[$level - 1][] = $createBranch
                    ? $createBranch($branches[$level])
                    : $branches[$level];
                $level--;
            } else {
                $branches[$level][] = $item;
            }
        }

        if ($level !== null) {
            throw new \Exception('not all branches of the tree are attached');
        }

        return $result;
    }

    public function wind()
    {
        return end($this->items);
    }

    public function rewind()
    {
        return reset($this->items);
    }

    public function slice(int $offset, ?int $length = null, bool $preserveKeys = false): self
    {
        return new self(array_slice($this->items,$offset, $length, $preserveKeys));
    }

    public function current()
    {
        return current($this->items);
    }

    public function next()
    {
        return next($this->items);
    }

    /**
     * @return int|string|null
     */
    public function key()
    {
        return key($this->items);
    }

    public function valid(): bool
    {
        return $this->key() !== null;
    }

    public function trim(\Closure $callback, ?bool $type = null): self
    {
        $leftPosition = $type === null or $type === false
            ? $this->search(static function($item, $key) use($callback) {
                return !$callback($item, $key);
            })
            : 0;
        $rightPosition = $type === null or $type === true
            ? $this->reverse()->search(static function($item, $key) use($callback) {
                return !$callback($item, $key);
            })
            : 0;

        return $this->slice(
            $leftPosition,
            $rightPosition === 0 ? null : - $rightPosition,
            true
        );
    }
}
