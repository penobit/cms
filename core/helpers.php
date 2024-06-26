<?php
/**
 * Penobit CMS Helpers file.
 * Author: R8
 * Author URL: https://penobit.com.
 */

use App\Application;
use App\Collection;
use App\Interfaces\Collection as CollectionInterface;
use App\Redirect;
use App\Request;
use App\Response;
use App\Template;
use App\UrlGenerator;
use Core\Routes\Router;

/**
 * Get the application instance.
 *
 * @return Application the application instance
 */
function app(): Application {
    return Application::$instance;
}

/**
 * Add an event to the application.
 *
 * @param string $event the name of the event
 * @param callable $callback the callback function to add
 * @param int $priority the priority of the callback (default 0)
 */
function listen(string $event, callable $callback, int $priority = 0) {
    app()->events->add($event, $callback, $priority);
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
 * Run an event in the application.
 *
 * @param string $event the name of the event
 * @param mixed ...$args The arguments to pass to the callback.
 *
 * @return mixed the result of running the event
 */
function dispatch($event, ...$args) {
    return app()->events->dispatch($event, ...$args);
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

function dump(...$args) {
    foreach ($args as $value) {
        echo '<pre>';
        var_dump($value);
        echo '</pre>';
    }
}

/**
 * Dump the given value with HTML formatting.
 *
 * @param mixed ...$args The values to dump.
 */
function dd(...$args) {
    dump(...$args);

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

/**
 * Get the response object from the application.
 *
 * This function is a helper to get the response object from the application.
 *
 * @return Response the response object
 */
function response(mixed $content = '', int $code = 200, array $headers = []): Response {
    return new Response($content, $code, $headers);
}

/**
 * Create a new collection from the given items.
 *
 * @param array $items the items to add to the collection
 *
 * @return CollectionInterface a collection containing the given items
 */
function collect(array $items): CollectionInterface {
    return new Collection($items);
}

/**
 * Create a new Template object for the specified template and data.
 *
 * @param string $template the path to the template file
 * @param mixed $data the data to be passed to the template
 *
 * @return Template the newly created Template object
 */
function view(string $template, mixed $data = null) {
    return new Template($template, $data);
}

/**
 * Generate a URL for the given path.
 *
 * @param null|string $path The path for the URL. If null, returns a new UrlGenerator instance.
 *
 * @return string|UrlGenerator the URL for the given path, or a new UrlGenerator instance
 */
function url(?string $path = null) {
    $urlGenerator = new UrlGenerator();

    if (is_null($path)) {
        return $urlGenerator;
    }

    return $urlGenerator->url($path);
}

/**
 * Get the name of the theme.
 *
 * This function retrieves the name of the current theme using the Application object.
 *
 * @return string the name of the theme
 */
function getThemeName() {
    // Get the name of the current theme using the Application object.
    return app()->getThemeName();
}

/**
 * Get the path to the theme.
 *
 * @param null|string $path the path to append to the theme path
 * @param null|string $theme The theme to use. If null, uses the current theme.
 *
 * @return string the path to the theme
 */
function getThemePath(?string $path = null, ?string $theme = null) {
    return app()->getThemePath($path, $theme);
}

/**
 * Include the theme's header file.
 */
function getThemeHeader() {
    include app()->getThemePath('header.php');
}

/**
 * Include the theme's footer file.
 */
function getThemeFooter() {
    include app()->getThemePath('footer.php');
}

/**
 * Redirects to a new path.
 *
 * @param null|string $path The path to redirect to
 * @param null|bool $permanent Whether the redirect is permanent or not
 */
function redirect(?string $path = null, ?bool $permanent = false): ?Redirect {
    if (func_num_args() === 1) {
        Redirect::to($path);

        return null;
    }

    return new Redirect($path, $permanent);
}

/**
 * Generate the URL for a given route.
 *
 * @param string $name The name of the route
 * @param mixed ...$args The parameters for the route
 *
 * @return string The generated URL
 */
function route($name, ...$args): string {
    $route = Router::getRouteByName($name, ...$args);

    if (!$route) {
        return false;
    }

    $path = $route->getPath();

    if (isset($args) && !empty($args)) {
        if (func_num_args() === 2 && is_array($args[0])) {
            $params = $args[0];

            // replace params in the path
            $path = preg_replace_callback('/\{(\w+)\??\}/', function($matches) use ($params) {
                if (isset($params[$matches[1]])) {
                    return $params[$matches[1]];
                }

                return '';
            }, $path);
        } else {
            // replace params in the path by their order
            foreach ($args as $value) {
                $path = preg_replace('/\{(\w+)\??\}/', $value, $path, 1);
            }
        }
    }

    $path = str_replace('//', '/', $path);
    $path = trim($path, '/');

    return url($path);
}

/**
 * A function that redirects the user back to the previous page if available, or to the home page if not.
 */
function back(): Redirect {
    $ref = $_SERVER['HTTP_REFERER'];

    if (isset($ref) && !empty($ref)) {
        return redirect($ref);
    }

    return redirect('/');
}