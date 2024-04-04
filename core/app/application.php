<?php

namespace App;

use Core\Routes\Router;

/**
 * The Application class represents the main entry point of the application.
 * It holds the application instance, the request object, the list of registered actions and filters, and the router instance.
 */
class Application {
    /**
     * The application's singular instance.
     *
     * @var self
     */
    public static $instance;

    /**
     * The application configuration.
     */
    public Config $config;

    /**
     * The list of registered actions.
     */
    public Actions $actions;

    /**
     * The list of registered filters.
     */
    public Filters $filters;

    /**
     * The request object.
     */
    public Request $request;

    /**
     * Initialize the application.
     *
     * @param Router $router the router instance
     * @param ServiceContainer $serviceContainer the service container instance
     */
    public function __construct(
        private Router $router,
        protected ServiceContainer $serviceContainer,
    ) {
        // Set the application instance
        self::$instance = $this;

        // Create a new serviceContainer for class autoloading
        $this->serviceContainer = new ServiceContainer();

        // Create a new list of registered actions
        $this->actions = new Actions();

        // Create a new list of registered filters
        $this->filters = new Filters();

        // Create a new request object
        $this->request = new Request();

        $this->config = new Config();
    }

    /**
     * Get the current instance.
     *
     * @return Application the application instance
     */
    public function getInstance(): static {
        return self::$instance;
    }

    /**
     * Get the router instance.
     *
     * @return Router the router instance
     */
    public function getRouter(): Router {
        return $this->router;
    }

    /**
     * Get the actions.
     *
     * @param string $action the action name
     *
     * @return array the list of registered actions for the given action name
     */
    public function getActions(string $action): array {
        return $this->actions->get($action);
    }

    /**
     * Add a new action.
     *
     * @param string $action the action name
     * @param callable $callback the callback function to be executed when the action is triggered
     * @param int $priority the priority of the action (higher priority actions are executed first)
     *
     * @return Application the application instance
     */
    public function addAction(string $action, callable $callback, int $priority = 10) {
        if (!isset($this->actions[$action])) {
            $this->actions[$action] = [];
        }

        $this->actions[$action][] = ['callback' => $callback, 'priority' => $priority];

        return $this;
    }

    /**
     * Get the filters.
     *
     * @param string $filter the filter name
     *
     * @return array the list of registered filters for the given filter name
     */
    public function getFilters(string $filter): array {
        return $this->filters->get($filter);
    }

    /**
     * Register the routes.
     * Include the routes defined in the core/routes/web.php file.
     */
    public function registerRoutes() {
        include HOME.'/core/routes/web.php';
    }

    /**
     * Set the application timezone.
     */
    public function setTimezone() {
        $timezone = $this->config->get('app.timezone');
        date_default_timezone_set($timezone);
    }

    /**
     * Set the application locale.
     */
    public function setLocale() {
        $locale = $this->config->get('app.locale');
        // setlocale(LC_ALL, $locale);
    }

    /**
     * Run the application.
     * Execute the router run method to handle the request.
     */
    public function run() {
        $this->registerRoutes();
        $this->setTimezone();
        $this->setLocale();
        $this->router->run();
    }

    /**
     * Resolve a given function/class with it's typed parameters.
     *
     * @param mixed $name the name of the function/class to resolve
     * @param mixed ...$args the list of arguments to pass to the function/class
     *
     * @return mixed the resolved instance of the function/class
     */
    public function resolve(mixed $name, mixed $args) {
        return $this->serviceContainer->resolve($name, $args);
    }

    /**
     * Bind a value to a specific key in the service container.
     *
     * @param string $key the key to bind the value to
     * @param mixed $value the value to bind
     */
    public function bind(string $key, mixed $value) {
        $this->serviceContainer->bind($key, $value);
    }
}
