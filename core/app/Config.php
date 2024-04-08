<?php

namespace App;

/**
 * Class Config
 * This class is responsible for handling application configurations.
 */
class Config {
    /**
     * @var array stores the retrieved configurations
     */
    private static $configs = [];

    /**
     * Retrieve a configuration value.
     *
     * @param string $key the configuration key
     * @param null|mixed $default the default value, if the configuration key is not found
     *
     * @return mixed the configuration value
     */
    public static function get($key, $default = null) {
        // Check if the configuration is already stored.
        if (!isset(self::$configs[$key])) {
            // Split the configuration key into segments.
            $segments = explode('.', $key);
            // Build the configuration file path.
            $file = $segments[0];
            $path = HOME."/configs/{$file}.php";
            // Retrieve the configuration value.
            self::$configs[$key] = static::retrieveConfig($path, $segments);
        }

        // Return the configuration value or the default value if not found.
        return self::$configs[$key] ?? $default;
    }

    /**
     * Retrieve a configuration value from a file.
     *
     * @param string $file the configuration file path
     * @param array $segments the configuration key segments
     *
     * @return mixed the configuration value
     */
    private static function retrieveConfig($file, $segments) {
        // Require the configuration file.
        $config = require $file;
        // Determine the number of segments.
        $segmentCount = count($segments) - 1;
        // Traverse the configuration structure.
        for ($i = 1; $i < $segmentCount; ++$i) {
            $config = $config[$segments[$i]];
        }

        // Return the final configuration value.
        return $config[$segments[$segmentCount]];
    }
}

