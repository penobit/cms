<?php

namespace Core\Routes;

/**
 * Class Route represents a route in the application.
 */
class Route {
    /**
     * @var null|string name of the route
     */
    public ?string $name;

    /**
     * @var null|string prefix of the route
     */
    public ?string $prefix;

    /**
     * @var array array of middlewares to be applied to this route
     */
    public array $middlewares = [];

    /**
     * @var string HTTP method of the route
     */
    public string $method;

    /**
     * @var string path of the route
     */
    public string $path;

    /**
     * @var array|callable callback for the route
     */
    public $callback;

    /**
     * Route constructor.
     *
     * @param string $method HTTP method of the route
     * @param string $path path of the route
     * @param array|callable|string $callback callback for the route
     */
    public function __construct(string $method, string $path, array|callable|string $callback) {
        $this->method = $method;
        $this->path = $path;
        $this->callback = $callback;
    }

    /**
     * Set the name of the route.
     *
     * @param string $name name of the route
     *
     * @return $this
     */
    public function name(string $name): self {
        $this->name = $name;

        return $this;
    }

    /**
     * Add middleware(s) to the route.
     *
     * @param array|callable|string $middleware middleware(s) to add
     *
     * @return $this
     */
    public function middleware(array|callable|string $middleware): self {
        if (is_array($middleware)) {
            foreach ($middleware as $m) {
                $this->middleware($m);
            }
        } else {
            $this->middlewares[] = $middleware;
        }

        return $this;
    }

    /**
     * Set the prefix of the route.
     *
     * @param string $prefix prefix of the route
     *
     * @return $this
     */
    public function prefix(string $prefix): self {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * Check if the route matches the given method and path.
     *
     * @param string $method The method of the request
     * @param string $path The path of the request
     *
     * @return bool if the route matches with uri and method
     */
    public function isMatch(string $method, string $path): bool {
        if ($this->method !== $method) {
            return false;
        }

        preg_match_all('/\{([a-zA-Z0-9_:]+)\??\}/', $this->getPath(), $matchedVariables);

        if (empty($matchedVariables[0])) {
            return trim($this->getPath(), '/') === trim($path, '/');
        }

        $pathParts = explode('/', trim($path, '/'));
        $routeParts = explode('/', trim($this->getPath(), '/'));

        if (count($pathParts) !== count($routeParts)) {
            return false;
        }

        for ($i = 0; count($pathParts) > $i; ++$i) {
            if (preg_match('/{([a-zA-Z0-9_:]+)\??\}/', $routeParts[$i])) {
                continue;
            }

            if ($pathParts[$i] != $routeParts[$i]) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the path of the route.
     *
     * @return string the path of the route
     */
    public function getPath(): string {
        return $this->path;
    }

    /**
     * Run the route callback with given parameters.
     */
    public function run(): mixed {
        $params = $this->resolveVariables();
        $callback = $this->callback;
        $callback = app()->resolve($callback, $params);

        return call_user_func($callback);
    }

    /**
     * Returns an array of variable names from the route's path.
     *
     * This method searches the route's path for variable placeholders
     * surrounded by curly braces and returns an array of the variable
     * names. If no variables are found, an empty array is returned.
     *
     * @return array an array of variable names from the route's path
     */
    public function getVariables(): array {
        preg_match_all('/\{([a-zA-Z0-9_:]+)\??\}/', $this->getPath(), $matchedVariables);

        return $matchedVariables[1] ?? [];
    }

    public function getVariable(string $name): string {
        $path = $this->getPath();
        $uri = request()->getUri();
        $pathParts = explode('/', trim($path, '/'));
        $uriParts = explode('/', trim($uri, '/'));
        $index = 0;

        foreach ($pathParts as $part) {
            if (preg_match("/{({$name})\\??\\}/", $part)) {
                return $uriParts[$index];
            }

            ++$index;
        }
    }

    public function resolveVariables(): array {
        $variables = [];
        $params = $this->getVariables();

        if (!empty($params)) {
            foreach ($params as $param) {
                $variables[$param] = $this->getVariable($param);
            }
        }

        return $variables;
    }
}

