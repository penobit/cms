<?php

namespace App;

use App\Exceptions\Handler;
use Core\Routes\Router;
use Database\Connection;
use Database\QueryBuilder;

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
     * The name of the theme to use for rendering the application's output.
     */
    private string $theme = 'default';

    /**
     * The locale of the application.
     */
    private string $locale;

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

        // Create a actions object
        $this->actions = new Actions();

        // Create a filters object
        $this->filters = new Filters();

        // Create a new request object
        $this->request = new Request();

        // Create a new config object
        $this->config = new Config();

        $this->locale = $this->config->get('app.locale') ?: 'en';
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
     *
     * @param null|string $locale the locale to set, or null to use the default locale
     */
    public function setLocale(?string $locale = null) {
        $locale = $locale ?: $this->config->get('app.locale');
        $this->locale = $locale;
        // setlocale(LC_ALL, $locale);

        return $this;
    }

    /**
     * Check if the caches directory exists, and if not, create it.
     */
    public function checkRequiredDirectories() {
        // Check if the caches directory exists, and if not, create it.
        if (!is_dir(CACHE_PATH)) {
            // Create the caches directory with read, write, execute permissions for all.
            mkdir(CACHE_PATH, 0777, true);
        }

        // Check if the logs directory exists, and if not, create it.
        if (!is_dir(LOGS_PATH)) {
            // Create the logs directory with read, write, execute permissions for all.
            mkdir(LOGS_PATH, 0777, true);
        }

        // Check if the template cache directory exists, and if not, create it.
        if (!is_dir(TEMPLATE_CACHE_PATH)) {
            // Create the template cache directory with read, write, execute permissions for all.
            mkdir(TEMPLATE_CACHE_PATH, 0777, true);
        }
    }

    /**
     * Run the application.
     * Execute the router run method to handle the request.
     */
    public function run() {
        // Register exception handler.
        $this->registerExceptionHandler();

        // Check required directories and create them if they don't exist.
        $this->checkRequiredDirectories();

        // Register routes.
        $this->registerRoutes();

        // Set the application timezone.
        $this->setTimezone();

        // Set the application locale.
        $this->setLocale();

        // Run the router to handle the request.
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

    /**
     * Check if the application is installed.
     *
     * This function checks if the 'users' table exists in the database.
     * If it doesn't exist and the current URI is not '/install',
     * it redirects the user to the installation page.
     */
    public function checkInstallation() {
        // Create a new query builder instance
        $q = new QueryBuilder();

        // Check if the 'users' table exists in the database
        $exists = $q->table('users')->exists();

        // If the table doesn't exist and the current URI is not '/install'
        if (!$exists && request()->getUri() !== 'install') {
            // Redirect the user to the installation page
            header('Location: /install');
        }
    }

    /**
     * Check the database connection.
     *
     * This function tries to establish a database connection using the Connection class.
     * If the connection fails, it prints an error message.
     *
     * @return $this the current Application instance
     */
    public function checkDatabaseConnection() {
        try {
            // Try to establish a database connection
            Connection::getConnection();

            // Return the current Application instance
            return $this;
        } catch (\PDOException $e) {
            // Print the database connection error message
            echo sprintf('Database connection error: ', $e->getMessage());
        }
    }

    public function registerExceptionHandler() {
        set_exception_handler([Handler::class, 'handle']);
    }

    /**
     * Get the template instance.
     *
     * @return Template the template instance
     */
    public function getTheme() {
        // Return the template instance
        return $this->theme;
    }

    /**
     * Set the template instance.
     *
     * @return $this the current Application instance
     */
    public function setTheme(string $theme) {
        // Set the template instance
        $this->theme = $theme;

        // Return the current Application instance
        return $this;
    }

    /**
     * Get the path to the theme.
     *
     * @param null|string $path the path to append to the theme path
     * @param null|string $theme The theme to use. If null, uses the current theme.
     *
     * @return string the path to the theme
     */
    public function getThemePath(?string $path = null, ?string $theme = null) {
        if (empty($theme)) {
            $theme = $this->theme;
        }

        return sprintf('%s/%s%s', THEMES_PATH, $theme, $path ? "/{$path}" : '');
    }

    /**
     * Get the URL of the theme.
     *
     * @param null|string $theme The theme to use. If null, uses the current theme.
     *
     * @return string the URL of the theme
     */
    public function getThemeUrl(?string $theme = null) {
        if (empty($theme)) {
            $theme = $this->theme;
        }

        return url(sprintf('content/themes/%s', $theme));
    }

    /**
     * Get the locale of the Application.
     *
     * @return string the locale of the Application
     */
    public function getLocale(): string {
        return $this->locale;
    }
}
