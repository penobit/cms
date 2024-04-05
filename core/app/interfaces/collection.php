<?php

namespace App\Interfaces;

/**
 * Interface Collection.
 *
 * Defines the contract for a collection of items.
 */
interface Collection {
    /**
     * Returns the items in the collection.
     *
     * @return array|Collection|iterable the items in the collection
     */
    public function getItems();

    /**
     * Sets the items in the collection.
     *
     * @param array|Collection|iterable $items the items to set in the collection
     */
    public function setItems(Collection|iterable $items);

    /**
     * Returns the number of items in the collection.
     *
     * @return int the number of items in the collection
     */
    public function count();

    /**
     * Returns whether the collection is empty.
     *
     * @return bool true if the collection is empty, false otherwise
     */
    public function isEmpty();

    /**
     * Filters the collection using the given callback.
     *
     * @param callable $callback the callback used to filter the collection
     *
     * @return Collection the filtered collection
     */
    public function filter(callable $callback);

    /**
     * Maps the collection using the given callback.
     *
     * @param callable $callback the callback used to map the collection
     *
     * @return Collection the mapped collection
     */
    public function map(callable $callback);

    /**
     * Reduces the collection using the given callback and initial value.
     *
     * @param callable $callback the callback used to reduce the collection
     * @param mixed $initial The initial value for the reduction. Defaults to null.
     *
     * @return mixed the reduced value
     */
    public function reduce(callable $callback, $initial = null);

    /**
     * Sorts the collection using the given callback.
     *
     * @param callable $callback the callback used to sort the collection
     *
     * @return Collection the sorted collection
     */
    public function sort(callable $callback);

    /**
     * Converts the collection to an array.
     *
     * @return array the array representation of the collection
     */
    public function toArray();

    /**
     * Converts the collection to a JSON string.
     *
     * @return string the JSON string representation of the collection
     */
    public function toJson();

    /**
     * Adds an item to the collection.
     *
     * @param mixed $item the item to be added
     */
    public function add($item);

    /**
     * Appends an item to the end of the collection.
     *
     * @param mixed $item the item to be appended
     */
    public function append($item);

    /**
     * Prepends an item to the beginning of the collection.
     *
     * @param mixed $item the item to be prepended
     */
    public function prepend($item);

    /**
     * Removes an item at the specified index from the collection.
     *
     * @param int $index the index of the item to be removed
     */
    public function remove(int $index);

    /**
     * Gets an item at the specified index from the collection.
     *
     * @param int $index the index of the item to be retrieved
     *
     * @return mixed the item at the specified index
     */
    public function get(int $index);

    /**
     * Retrieves the first item of the collection.
     *
     * @return mixed The first item of the collection
     */
    public function first();

    /**
     * Retrieves the last item of the collection.
     *
     * @return mixed The last item of the collection
     */
    public function last();

    /**
     * Retrieves a slice of the collection between $start and $end (inclusive).
     *
     * @param int $start the index from which to start the slice
     * @param null|int $end the index at which to end the slice. Defaults to null to get the rest of the collection.
     *
     * @return Collection the sliced collection
     */
    public function slice(int $start, ?int $end = null);

    /**
     * Reverses the order of the items in the collection.
     *
     * @return Collection the reversed collection
     */
    public function reverse();

    /**
     * Removes and returns the first item of the collection.
     *
     * @return mixed the removed item
     */
    public function shift();

    /**
     * Removes and returns the last item of the collection.
     *
     * @return mixed the removed item
     */
    public function pop();

    /**
     * Retrieves a random item from the collection.
     *
     * @return mixed a random item from the collection
     */
    public function random();

    /**
     * Shuffles the items in the collection.
     *
     * @return Collection the shuffled collection
     */
    public function shuffle();

    /**
     * Returns a new collection with duplicate items removed.
     *
     * @return Collection the collection with duplicate items removed
     */
    public function unique();
}
