<?php

use App\Application;

/**
 * Penobit CMS Helpers file.
 * Author: R8
 * Author URL: https://penobit.com.
 */
function app(): Application {
    return Application::$instance;
}

function addAction(string $action, callable $callback, int $priority = 0) {
    app()->actions->add($action, $callback, $priority);
}

function addFilter(string $filter, callable $callback, int $priority = 0) {
    app()->filters->add($filter, $callback, $priority);
}

function runAction($action, ...$args) {
    return app()->actions->run($action, ...$args);
}

function applyFilter($filter, ...$args) {
    return app()->filters->apply($filter, ...$args);
}

function config($key, $default = null) {
    return app()->config->get($key, $default);
}