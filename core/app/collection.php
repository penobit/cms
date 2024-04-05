<?php

namespace App;

use App\Interfaces\Collection as CollectionInterface;

/**
 * Class Collection.
 *
 * A collection represents a group of items. It provides various methods
 * to manipulate and retrieve the items stored in the collection.
 * This class implements the `CollectionInterface` and is used
 * to encapsulate and manipulate arrays of items.
 */
class Collection implements CollectionInterface {
    protected array $items = [];

    /**
     * Create a new collection.
     */
    public function __construct(array $items = []) {
        $this->items = $items;
    }

    /**
     * Returns a JSON encoded string of the items in the collection.
     */
    public function __toString(): string {
        return $this->toJSON();
    }

    /**
     * Get all of the items in the collection.
     */
    public function all(): array {
        return $this->items;
    }

    /**
     * Get the first item from the collection.
     */
    public function first(): mixed {
        return reset($this->items);
    }

    /**
     * Get the last item from the collection.
     */
    public function last(): mixed {
        return end($this->items);
    }

    /**
     * Determine if the collection is empty or not.
     */
    public function isEmpty(): bool {
        return empty($this->items);
    }

    /**
     * Get the number of items in the collection.
     */
    public function count(): int {
        return count($this->items);
    }

    /**
     * Map the collection to a new collection with the provided callback.
     *
     * @return Collection
     */
    public function map(callable $callback): static {
        return new static(array_map($callback, $this->items));
    }

    /**
     * Filter the collection using the provided callback.
     *
     * @return Collection
     */
    public function filter(callable $callback): static {
        return new static(array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH));
    }

    /**
     * Reduce the collection to a single value using the provided callback.
     *
     * @param null|mixed $initial
     */
    public function reduce(callable $callback, $initial = null) {
        return array_reduce($this->items, $callback, $initial);
    }

    /**
     * Chunk the collection into arrays of the specified size.
     *
     * @return Collection
     */
    public function chunk(int $size): static {
        return new static(array_chunk($this->items, $size));
    }

    /**
     * Get a subset of items from the collection.
     *
     * @return Collection
     */
    public function slice(int $offset, ?int $length = null): static {
        return new static(array_slice($this->items, $offset, $length));
    }

    /**
     * Get all items in the collection.
     */
    public function getItems(): array {
        return $this->items;
    }

    /**
     * Set the items in the collection.
     *
     * @return self
     */
    public function setItems(CollectionInterface|iterable $items): static {
        $this->items = $items;

        return $this;
    }

    /**
     * Sort the items in the collection.
     *
     * @return self
     */
    public function sort(?callable $comparator = null): static {
        if ($comparator) {
            usort($this->items, $comparator);
        } else {
            sort($this->items);
        }

        return $this;
    }

    /**
     * Convert the collection to array.
     */
    public function toArray(): array {
        return $this->items;
    }

    /**
     * Convert the collection to JSON.
     */
    public function toJson(): string {
        return json_encode($this->items, Response::$jsonResponseFlags);
    }

    /**
     * Add an item to the collection.
     *
     * @return self
     */
    public function add($item): static {
        $this->items[] = $item;

        return $this;
    }

    /**
     * Append multiple items to the collection.
     *
     * @return self
     */
    public function append(mixed $items): static {
        $items = is_iterable($items) ? $items : [$items];
        $this->items = array_merge($this->items, $items);

        return $this;
    }

    /**
     * Prepend multiple items to the collection.
     *
     * @return self
     */
    public function prepend(mixed $items): static {
        $items = is_iterable($items) ? $items : [$items];
        $this->items = array_merge($items, $this->items);

        return $this;
    }

    /**
     * Remove an item from the collection by key.
     *
     * @return self
     */
    public function remove($key): static {
        unset($this->items[$key]);
        $this->items = array_values($this->items);

        return $this;
    }

    /**
     * Get an item from the collection by key.
     */
    public function get($key): mixed {
        return $this->items[$key] ?? null;
    }

    /**
     * Reverse the order of the items in the collection.
     *
     * @return self
     */
    public function reverse(): static {
        $this->items = array_reverse($this->items);

        return $this;
    }

    /**
     * Remove and return the first item from the collection.
     */
    public function shift(): mixed {
        return array_shift($this->items);
    }

    /**
     * Remove and return the last item from the collection.
     */
    public function pop(): mixed {
        return array_pop($this->items);
    }

    /**
     * Get a random item from the collection.
     */
    public function random(): mixed {
        return $this->items[array_rand($this->items)];
    }

    /**
     * Shuffle the items in the collection.
     *
     * @return self
     */
    public function shuffle(): static {
        shuffle($this->items);

        return $this;
    }

    /**
     * Remove duplicate items from the collection.
     *
     * @return self
     */
    public function unique(): static {
        $this->items = array_unique($this->items);

        return $this;
    }
}
