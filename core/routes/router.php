<?php

namespace Core\Routes;

use App\Exceptions\PageNotFoundException;
use App\Request;
use App\Response;

class Router {
    /**
     * A list of registered routes.
     * Each route is an instance of the `Route` class.
     *
     * @var array<Route>
     */
    private static $routes = [];

    /**
     * The request object used to get the current HTTP method and URI.
     */
    private Request $request;

    /**
     * Constructs a new Router instance.
     * This constructor initializes the `request` property with a new instance of the `Request` class.
     * The `Request` class is used to get the current HTTP method and URI.
     */
    public function __construct() {
        $this->request = new Request();
    }

    /**
     * Retrieves the list of registered routes.
     *
     * @return array<Route> the list of registered routes
     */
    public static function getRoutes() {
        return static::$routes;
    }

    /**
     * Retrieves a specific route by its name.
     *
     * @param string $name the name of the route to retrieve
     *
     * @return null|Route the route with the given name, or null if not found
     */
    public static function getRouteByName(string $name) {
        // Loop through each route in the list of registered routes
        foreach (static::$routes as $route) {
            // Check if the name of the current route matches the given name
            if ($route->getName() == $name) {
                // If a match is found, return the route
                return $route;
            }
        }

        // If no match was found, return null
        return null;
    }

    /**
     * Registers a new route for the given HTTP method and path.
     *
     * @param string $method the HTTP method to register the route for
     * @param string $path The URL path to register for (e.g., "/users", "/users/:id").
     * @param array|callable|string $callback the callback to execute when the route is matched
     *
     * @return Route the Route object that was registered
     */
    public static function route(string $method, string $path, array|callable|string $callback) {
        // Creates a new Route object with the given HTTP method, URL path, and callback.
        // The Route object is then passed through the 'router/route' filter and added to the list of routes.
        // Returns the Route object that was registered.
        $route = new Route($method, $path, $callback);
        $route = applyFilter('router/new-route', $route);

        static::$routes[] = $route;

        return $route;
    }

    /**
     * Registers a new route for the given HTTP method and path.
     *
     * @param string $path The URL path to register for (e.g., "/users", "/users/:id").
     * @param array|callable|string $callback the callback to execute when the route is matched
     *
     * @return Route the Route object that was registered
     */
    public static function get(string $path, array|callable|string $callback) {
        return static::route('GET', $path, $callback);
    }

    /**
     * Registers a new route for the HTTP POST method and given path.
     *
     * @param string $path The URL path to register for (e.g., "/users", "/users/:id").
     * @param array|callable|string $callback the callback to execute when the route is matched
     *
     * @return Route the Route object that was registered
     */
    public static function post(string $path, array|callable|string $callback) {
        return static::route('POST', $path, $callback);
    }

    /**
     * Registers a new route for the HTTP PUT method and given path.
     *
     * @param string $path The URL path to register for (e.g., "/users", "/users/:id").
     * @param array|callable|string $callback the callback to execute when the route is matched
     *
     * @return Route the Route object that was registered
     */
    public static function put(string $path, array|callable|string $callback) {
        return static::route('PUT', $path, $callback);
    }

    /**
     * Registers a new route for the HTTP DELETE method and given path.
     *
     * @param string $path The URL path to register for (e.g., "/users", "/users/:id").
     * @param array|callable|string $callback the callback to execute when the route is matched
     *
     * @return Route the Route object that was registered
     */
    public static function delete(string $path, array|callable|string $callback) {
        return static::route('DELETE', $path, $callback);
    }

    /**
     * Registers a new route for the HTTP PATCH method and given path.
     *
     * @param string $path The URL path to register for (e.g., "/users", "/users/:id").
     * @param array|callable|string $callback the callback to execute when the route is matched
     *
     * @return Route the Route object that was registered
     */
    public static function patch(string $path, array|callable|string $callback) {
        return static::route('PATCH', $path, $callback);
    }

    /**
     * Registers a new route for the HTTP OPTIONS method and given path.
     *
     * @param string $path The URL path to register for (e.g., "/users", "/users/:id").
     * @param array|callable|string $callback the callback to execute when the route is matched
     *
     * @return Route the Route object that was registered
     */
    public static function options(string $path, array|callable|string $callback) {
        return static::route('OPTIONS', $path, $callback);
    }

    /**
     * Registers a new route for any HTTP method and given path.
     *
     * @param string $path The URL path to register for (e.g., "/users", "/users/:id").
     * @param array|callable|string $callback the callback to execute when the route is matched
     *
     * @return Route the Route object that was registered
     */
    public static function any(string $path, array|callable|string $callback) {
        return static::route('*', $path, $callback);
    }

    /**
     * Run the router.
     */
    public function run() {
        $route = $this->matchRoute();

        if (!$route) {
            throw new PageNotFoundException();
        }

        $this->sendResponse($route->run());
    }

    /**
     * Sends the response to the client.
     *
     * @param mixed $response The response to send. Can be anything, but will be cast to a Response object if it's not already.
     *
     * @return null|string the response sent to the client, or null if no response was given
     */
    public function sendResponse(mixed $response): ?string {
        // If a response was provided and it's not already a Response object, cast it to one.
        // Then send the response and return it.
        // If no response was provided, return null.
        if (isset($response) && !empty($response)) {
            if (!$response instanceof Response) {
                $response = response($response);
            }

            return $response->send();
        }

        return null;
    }

    /**
     * Matches a route based on the request method and URI.
     *
     * @return null|Route the matched route, or null if no match was found
     */
    public function matchRoute(): ?Route {
        // Get the request URI and method.
        $uri = $this->request->getUri();
        $method = $this->request->getMethod();

        // Loop through the registered routes and check if any matches the request.
        foreach (static::$routes as $route) {
            // Check if the route matches the request method and URI.
            $match = $route->isMatch($method, $uri);

            // If a match is found, return the route.
            if ($match) {
                return $route;
            }
        }

        // If no match was found, return null.
        return null;
    }
}
