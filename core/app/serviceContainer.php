<?php

namespace App;

use App\Entities\Entity;
use Database\QueryBuilder;
use Exception;

/**
 * Class ServiceContainer.
 *
 * This class provides a service container for dependency injection.
 */
class ServiceContainer {
    /**
     * An associative array containing the bindings of the service container.
     */
    protected static array $bindings = [
        'Database\\QueryBuilder' => QueryBuilder::class,
    ];

    /**
     * Resolve the given class.
     *
     * @param mixed $class The class name to resolve
     *
     * @return mixed The resolved class or false if the class does not exist
     *
     * @throws ReflectionException if the class does not exist or if there is a reflection error
     */
    public function resolveClass(mixed $class, mixed $args = []): mixed {
        // If the name is bound, return the bound value
        if (isset($this->bindings[$class])) {
            return $this->bindings[$class];
        }

        // If the class is a callable, return it as is
        if (is_callable($class)) {
            return $class;
        }

        // If the class exists, instantiate it and return it
        if (class_exists($class)) {
            return new $class();
        }

        // If the class file exists, include it and instantiate it
        $classPath = sprintf('%s.php', strtolower($class));
        if (file_exists($classPath)) {
            include $classPath;
        }

        if (class_exists($class)) {
            return new $class();
        }

        return false;
    }

    /**
     * Resolve the given function.
     *
     * @param mixed $function The function name to resolve
     *
     * @return Closure The resolved function
     *
     * @throws InvalidArgumentException if the function is not callable
     */
    public function resolveFunction(mixed $function, mixed $args = []) {
        // If the name is bound, return the bound value
        if (isset($this->bindings[$function])) {
            return $this->bindings[$function];
        }

        // If the function is not callable, throw an exception
        if (!is_callable($function)) {
            throw new \InvalidArgumentException(sprintf('Function "%s" is not callable', $function));
        }

        // Reflect the function
        $reflection = new \ReflectionFunction($function);
        $params = $reflection->getParameters();

        // If the function has no parameters, return it as is
        if (empty($params)) {
            return $function;
        }

        // Resolve the parameters and bind them to the function
        $newInstanceParams = [];
        foreach ($params as $param) {
            if ($param->getClass() === null) {
                if (count($args) > 0 && isset($args[$param->name])) {
                    $value = $args[$param->name];
                } else {
                    $value = $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null;
                    // try {
                    // } catch (\ReflectionException) {
                    //     $value = null;
                    // }
                }

                $newInstanceParams[] = $value;
            } else {
                // check if class is Entity or it inherits Entity
                $class = $param->getClass()->getName();
                if (class_exists($class) && is_subclass_of($class, Entity::class)) {
                    dd($class, $param->getName(), $param);
                }

                $newInstanceParams[] = $this->resolve(
                    $param->getClass()->getName()
                );
            }
        }

        // Bind the parameters to the function and return it
        return fn () => $reflection->getClosure()->bindTo(null, null)->__invoke(...$newInstanceParams);
    }

    /**
     * Resolve the given class/function and it's typed parameters.
     */
    public function resolve(mixed $name, mixed $args = []) {
        // If the name is a string, resolve it as a class
        if (is_string($name)) {
            return $this->resolveClass($name, $args);
        }

        // Resolve it as a function
        return $this->resolveFunction($name, $args);
    }

    /**
     * Register the autoloader for the service container.
     */
    public function register() {
        spl_autoload_register(fn ($class) => $this->resolveClass($class));
    }

    /**
     * Bind a value to a key in the service container.
     */
    public function bind($key, $value) {
        $this->bindings[$key] = $value;
    }

    /**
     * Unbind a value from a key in the service container.
     */
    public function unbind($key) {
        unset($this->bindings[$key]);
    }
}
