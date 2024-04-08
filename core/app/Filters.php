<?php

namespace App;

/**
 * Class Filters.
 *
 * This class handles the application of filters to values.
 */
class Filters {
    /** @var array The list of registered filters */
    private $filters = [];

    /**
     * Check if filter exists.
     *
     * @param string $filter the name of the filter to check
     *
     * @return bool true if the filter exists, false otherwise
     */
    public function has(string $filter): bool {
        return array_key_exists($filter, $this->filters);
    }

    /**
     * Add callback to filter.
     *
     * @param string $filter the name of the filter to add callback to
     * @param callable $callback the callback function to add
     * @param int $priority the priority of the callback (default 10)
     *
     * @return self the current instance of the Filters class
     */
    public function add(string $filter, callable $callback, int $priority = 10): self {
        if (!$this->has($filter)) {
            $this->filters[$filter] = [];
        }

        $this->filters[$filter][$priority][] = $callback;

        return $this;
    }

    /**
     * Get specific filter callbacks.
     *
     * @param string $filter the name of the filter to get callbacks for
     *
     * @return null|array an array of callbacks for the filter, or null if the filter does not exist
     */
    public function get(string $filter): ?array {
        return $this->filters[$filter] ?? [];
    }

    /**
     * Apply filters to a value.
     *
     * @param string $filter the name of the filter to apply
     * @param mixed ...$args The arguments to pass to the callbacks.
     *
     * @return mixed the result of applying the filters to the value
     */
    public function apply(string $filter, ...$args): mixed {
        $callbacks = $this->get($filter);

        usort($callbacks, function($a, $b) {
            return $a['priority'] <=> $b['priority'];
        });

        $value = $args[0];

        foreach ($callbacks as $callback) {
            $value = call_user_func_array($callback['callback'], $args);
        }

        return $value;
    }
}
