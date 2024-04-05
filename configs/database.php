<?php

/**
 * Configuration file for database settings.
 *
 * This file contains settings for connecting to a MySQL database.
 * The configuration is based on the PDO library and it is used by the framework.
 *
 * @return array the database configuration
 */

return [
    'driver' => 'mysql', // mysql, sqlite
    'host' => 'localhost',
    'port' => '3306',
    'username' => 'root',
    'password' => '',
    'database' => 'penobit',
    'prefix' => '',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'engine' => 'InnoDB',
    'debug' => false,
];