<?php

namespace Database;

/**
 * Represents a database connection.
 */
class Connection {
    /**
     * Holds the singleton PDO instance.
     *
     * @var null|\PDO the PDO instance representing the database connection
     */
    protected static $connection;

    /**
     * Constructor for the Connection class.
     *
     * Calls the static `connect` method to establish a database connection.
     */
    public function __construct() {
        // Establish a database connection.
        static::connect();
    }

    /**
     * Returns the PDO database connection instance.
     *
     * This method uses a singleton pattern to ensure that only one instance of the PDO
     * object is created, even in a multi-threaded environment.
     *
     * @return \PDO the PDO database connection instance
     */
    public static function getConnection() {
        // If no connection has been established yet, create a new PDO instance.
        if (!isset(self::$connection)) {
            // Create a new PDO instance and save it in the static property.
            self::$connection = static::connect();
        }

        // Return the PDO instance.
        return self::$connection;
    }

    /**
     * Connects to the database using the provided configuration.
     *
     * @return \PDO the PDO instance representing the database connection
     */
    public static function connect() {
        // Check if the driver is SQLite.
        if (config('database.driver') == 'sqlite') {
            // Create a new PDO instance for SQLite and return it.
            return new \PDO('sqlite:'.config('database.database'));
        }

        // Retrieve the database configuration values.
        $host = config('database.host');
        $database = config('database.database');
        $username = config('database.username');
        $password = config('database.password');

        // Create a new PDO instance for MySQL and return it.
        return new \PDO("mysql:host={$host};dbname={$database}", $username, $password);
    }
}

