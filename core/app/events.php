<?php

namespace App;

class Events {
    /**
     * App\Events is a class that provides a way to define and run callbacks for
     * specific named events.
     *
     * Use it to hook into system events and extend the behavior of the
     * application.
     *
     * The class is a simple implementation of the Observer pattern.
     *
     * You can add callbacks to events, and run these callbacks later.
     * Callbacks are functions that will be called later in the execution
     * chain, and they will receive the same arguments that were passed to the
     * run method.
     *
     * Callbacks in an event are called in order of priority, from lowest to
     * highest.
     *
     * The class is not thread-safe, and is not meant to be used in a
     * multi-threaded environment.
     *
     * This class is not intended to be instantiated directly. Instead, use
     * App::getEvents() to obtain an instance of the Events class.
     *
     * @link App::getEvents()
     */
    private $events = [];

    /**
     * Check if event exists.
     *
     * @param string $event the name of the event to check
     *
     * @return bool true if the event exists, false otherwise
     */
    public function has(string $event): bool {
        return array_key_exists($event, $this->events);
    }

    /**
     * Add callback to event.
     *
     * @param string $event the name of the event to add to
     * @param callable $callback the callback to add to the event
     * @param int $priority the priority of the callback (default 10)
     *
     * @return self the instance of the Events class
     */
    public function add(string $event, callable $callback, int $priority = 10): self {
        if (!$this->has($event)) {
            $this->events[$event] = [];
        }

        $this->events[$event][$priority][] = $callback;

        return $this;
    }

    /**
     * Get specific event callbacks.
     *
     * @param string $event the name of the event to get callbacks for
     *
     * @return null|array the callbacks for the event, or null if the event does not exist
     */
    public function get(string $event): ?array {
        return $this->events[$event] ?? [];
    }

    /**
     * Run callback of a specific event.
     *
     * @param string $event the name of the event to run
     * @param mixed ...$args The arguments to pass to the callbacks.
     */
    public function run(string $event, ...$args): void {
        $callbacks = $this->get($event);

        usort($callbacks, function($a, $b) {
            return $a['priority'] <=> $b['priority'];
        });

        foreach ($callbacks as $callback) {
            call_user_func_array($callback['callback'], $args);
        }
    }
}
