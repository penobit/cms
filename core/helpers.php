<?php

use App\Application;
use App\Request;

/**
 * Penobit CMS Helpers file.
 * Author: R8
 * Author URL: https://penobit.com.
 */
/**
 * Get the application instance.
 *
 * @return Application the application instance
 */
function app(): Application {
    return Application::$instance;
}

/**
 * Add an action to the application.
 *
 * @param string $action the name of the action
 * @param callable $callback the callback function to add
 * @param int $priority the priority of the callback (default 0)
 */
function addAction(string $action, callable $callback, int $priority = 0) {
    app()->actions->add($action, $callback, $priority);
}

/**
 * Add a filter to the application.
 *
 * @param string $filter the name of the filter
 * @param callable $callback the callback function to add
 * @param int $priority the priority of the callback (default 0)
 */
function addFilter(string $filter, callable $callback, int $priority = 0) {
    app()->filters->add($filter, $callback, $priority);
}

/**
 * Run an action in the application.
 *
 * @param string $action the name of the action
 * @param mixed ...$args The arguments to pass to the callback.
 *
 * @return mixed the result of running the action
 */
function runAction($action, ...$args) {
    return app()->actions->run($action, ...$args);
}

/**
 * Apply filters to a value in the application.
 *
 * @param string $filter the name of the filter
 * @param mixed ...$args The arguments to pass to the callback.
 *
 * @return mixed the result of applying the filters to the value
 */
function applyFilter($filter, ...$args) {
    return app()->filters->apply($filter, ...$args);
}

/**
 * Get a configuration value from the application.
 *
 * @param string $key the configuration key
 * @param mixed $default the default value to return if the key is not found (default null)
 *
 * @return mixed the configuration value, or the default value if the key is not found
 */
function config($key, $default = null) {
    return app()->config->get($key, $default);
}

/**
 * Dump the given value with HTML formatting.
 *
 * @param mixed ...$args The values to dump.
 */
function dd(...$args) {
    foreach ($args as $value) {
        echo '<pre>';
        var_dump($value);
        echo '</pre>';
    }

    exit;
}

/**
 * Get the request object from the application.
 *
 * This function is a helper to get the request object from the application.
 *
 * @return Request the request object
 */
function request(): Request {
    return app()->request;
}
