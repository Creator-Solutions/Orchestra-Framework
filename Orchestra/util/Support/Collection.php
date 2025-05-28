<?php

namespace Orchestra\util\Support;

/**
 * Class SimpleCollection
 *
 * A lightweight wrapper around arrays that provides utility methods
 * similar to Laravel's Collection, such as map, filter, and every.
 *
 * This is a standalone implementation with no dependencies.
 *
 * @package App\Support
 * @author creator-solution/owen
 */
class Collection
{
    protected array $items;

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public function every(callable $callback): bool
    {
        foreach ($this->items as $item) {
            if (!$callback($item)) {
                return false;
            }
        }
        return true;
    }

    public function map(callable $callback): Collection
    {
        return new self(array_map($callback, $this->items));
    }

    public function filter(callable $callback): Collection
    {
        return new self(array_filter($this->items, $callback));
    }

    public function all(): array
    {
        return $this->items;
    }
}