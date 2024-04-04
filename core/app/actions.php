<?php

namespace App;

class Actions {
    /**
     * App\Actions is a class that provides a way to define and run callbacks for
     * specific named actions.
     *
     * Use it to hook into system events and extend the behavior of the
     * application.
     *
     * The class is a simple implementation of the Observer pattern.
     *
     * You can add callbacks to actions, and run these callbacks later.
     * Callbacks are functions that will be called later in the execution
     * chain, and they will receive the same arguments that were passed to the
     * run method.
     *
     * Callbacks in an action are called in order of priority, from lowest to
     * highest.
     *
     * The class is not thread-safe, and is not meant to be used in a
     * multi-threaded environment.
     *
     * This class is not intended to be instantiated directly. Instead, use
     * App::getActions() to obtain an instance of the Actions class.
     *
     * @link App::getActions()
     */
    private $actions = [];

    /**
     * Check if action exists.
     *
     * @param string $action the name of the action to check
     *
     * @return bool true if the action exists, false otherwise
     */
    public function has(string $action): bool {
        return array_key_exists($action, $this->actions);
    }

    /**
     * Add callback to action.
     *
     * @param string $action the name of the action to add to
     * @param callable $callback the callback to add to the action
     * @param int $priority the priority of the callback (default 10)
     *
     * @return self the instance of the Actions class
     */
    public function add(string $action, callable $callback, int $priority = 10): self {
        if (!$this->has($action)) {
            $this->actions[$action] = [];
        }

        $this->actions[$action][$priority][] = $callback;

        return $this;
    }

    /**
     * Get specific action callbacks.
     *
     * @param string $action the name of the action to get callbacks for
     *
     * @return null|array the callbacks for the action, or null if the action does not exist
     */
    public function get(string $action): ?array {
        return $this->actions[$action] ?? [];
    }

    /**
     * Run callback of a specific action.
     *
     * @param string $action the name of the action to run
     * @param mixed ...$args The arguments to pass to the callbacks.
     */
    public function run(string $action, ...$args): void {
        $callbacks = $this->get($action);

        usort($callbacks, function($a, $b) {
            return $a['priority'] <=> $b['priority'];
        });

        foreach ($callbacks as $callback) {
            call_user_func_array($callback['callback'], $args);
        }
    }
}
