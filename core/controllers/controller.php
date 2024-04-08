<?php

namespace App\Controllers;

/**
 * Class Controller.
 *
 * This class represents a base controller in the application.
 * other controllers will extend this class.
 */
class Controller {
    /**
     * Middlewares to be applied to the controller's methods.
     *
     * @var array<int, mixed> an array of middleware classes or callables
     */
    protected array $middlewares = [];

    public function __construct() {}

    /**
     * Authorize the given action for the given class.
     *
     * @param string $action the action to be authorized
     * @param string $class the class for which the authorization is done
     */
    public function authorize(string $action, string $class) {
        // Implementation details...
    }

    /**
     * Retrieves the middlewares registered for this controller.
     *
     * @return array the array of middlewares registered for this controller
     */
    public function getMiddlewares(): array {
        return $this->middlewares;
    }

    /**
     * Registers a new middleware for this controller.
     *
     * @param mixed $middleware the middleware to register
     */
    public function middleware(mixed $middleware): void {
        $this->middlewares[] = $middleware;
    }

    /**
     * A description of the entire PHP function.
     *
     * @param string $view description
     * @param array $data description
     *
     * @return ReturnType
     */
    public function view(string $view, array $data = []) {
        return view($view, $data);
    }

    /**
     * Redirects to a specified URL.
     *
     * @param null|string $to The URL to redirect to
     * @param null|bool $permanent Whether the redirect is permanent
     */
    public function redirect(?string $to, ?bool $permanent = false) {
        return redirect($to, $permanent);
    }

    /**
     * A description of the entire PHP function.
     *
     * @return Some_Return_Value
     *
     * @throws Some_Exception_Class description of exception
     */
    public function back() {
        return back();
    }
}
