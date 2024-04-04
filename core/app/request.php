<?php

namespace App;

/**
 * Class Request
 * This class represents the request data from the client.
 */
class Request {
    /**
     * Constructor of the Request class.
     */
    public function __construct() {}

    /**
     * Returns the value of a specific key from the request data.
     *
     * @param string $key the key to search for
     * @param mixed $default the default value to return if the key is not found
     *
     * @return mixed the value of the key or the default value
     */
    public function input(string $key, $default = null) {
        // Check if the key exists in the request data
        if (isset($_REQUEST[$key])) {
            return $_REQUEST[$key];
        }

        // If the key contains a dot, we need to handle nested data
        if (str_contains($key, '.')) {
            // Explode the key by dot and navigate through the nested data
            $key = explode('.', $key);

            while (count($key) > 1) {
                $key = array_shift($key);
                if (isset($_REQUEST[$key])) {
                    $value = $_REQUEST[$key];
                } else {
                    return $default;
                }
            }
        }

        return $default;
    }

    /**
     * Returns all the request data.
     *
     * @return array the request data
     */
    public function all() {
        // Get all the request data
        $res = $_REQUEST;
        // Apply a filter to the request data
        $res = applyFilter('request/all', $res);

        return $res;
    }

    /**
     * Returns the URI of the request.
     *
     * @return string the URI of the request
     */
    public function getUri() {
        // Get the URI from the server variable REQUEST_URI
        $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        // Parse the URI and get the path
        $uri = parse_url($uri, PHP_URL_PATH);
        // Trim the leading and trailing slashes from the URI
        $uri = trim($uri, '/');
        // Apply a filter to the URI
        $uri = applyFilter('request/uri', $uri);

        return $uri;
    }

    /**
     * Returns the HTTP method of the request.
     *
     * @return string the HTTP method of the request
     */
    public function getMethod() {
        // Get the HTTP method from the server variable REQUEST_METHOD
        $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';
        // Apply a filter to the HTTP method
        $method = applyFilter('request/method', $method);

        return $method;
    }
}
