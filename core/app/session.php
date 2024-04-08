<?php

namespace App;

/**
 * Class Session.
 *
 * This class represents a session, which is a way to store data
 * for a user across multiple requests.
 */
class Session {
    public function __construct() {}

    /**
     * Starts a session if one is not already started.
     */
    public static function start() {
        // Check if a session is not already started
        if (session_status() === PHP_SESSION_NONE) {
            // Set the session name to 'penobit'
            session_name('penobit');
            // Start the session
            session_start();
        }
    }

    /**
     * Retrieves a value from the session.
     *
     * @param string $key The key of the value to retrieve
     * @param mixed $default The default value to return if the key is not found
     *
     * @return mixed The value associated with the key, or the default value
     */
    public function get(string $key, $default = null) {
        // Retrieve the value from the session
        $value = $_SESSION[$key];

        // Check if the value is a string and if it looks like JSON or serialized data
        if (is_string($value)) {
            if (strpos($value, '{') === 0 || strpos($value, '[') === 0) {
                // Attempt to decode the value as JSON
                $json = json_decode($value, true);
                // If successful, update the value
                $value = $json ?? $value;
            }

            // Check if the value contains a vertical bar, indicating serialized data
            if (strpos($value, '|') !== false) {
                // Attempt to unserialize the value
                $unserialize = unserialize($value);
                // If successful, update the value
                $value = $unserialize ?? $value;
            }
        }

        // Return the value or the default if it is not found
        return $value ?? $default;
    }

    /**
     * Sets a value in the session.
     *
     * @param array|string $key The key of the value to set, or an array of key-value pairs
     * @param mixed $value The value to set, if a single key is provided
     *
     * @return self For method chaining
     */
    public function set(array|string $key, $value = null) {
        // If a single key-value pair is provided, set it in the session
        if (func_num_args() === 1 && is_array($key)) {
            foreach ($key as $k => $v) {
                $_SESSION[$k] = $v;
            }
        } else {
            // Otherwise, set the key-value pair in the session
            $_SESSION[$key] = $value;
        }

        // Return the instance for method chaining
        return $this;
    }

    /**
     * Checks if a key exists in the session.
     *
     * @param string $key The key to check
     *
     * @return bool True if the key exists, false otherwise
     */
    public function has(string $key) {
        // Check if the key exists in the session
        return isset($_SESSION[$key]);
    }

    /**
     * Remove one or more keys from the session.
     *
     * @param array|string $key The key(s) to remove, can be a string or array of strings
     *
     * @return self For method chaining
     */
    public function remove(array|string $key) {
        // If an array of keys is provided, remove each key
        if (is_array($key)) {
            foreach ($key as $k) {
                // If the key exists in the session, remove it
                if ($this->has($k)) {
                    unset($_SESSION[$k]);
                }
            }
        } else {
            // If a single key is provided, remove it if it exists in the session
            if ($this->has($key)) {
                unset($_SESSION[$key]);
            }
        }

        // Return the instance for method chaining
        return $this;
    }

    /**
     * Destroy the session and unset all session data.
     */
    public function destroy() {
        // Clear the session data
        $this->clear();

        // Destroy the session
        session_destroy();

        // Unset all session variables
        session_unset();

        // Regenerate the session ID
        session_regenerate_id(true);
    }

    /**
     * Store a value in the session flash, which will be available in the next request,
     * but not in the one after that.
     *
     * @param array|string $key The key(s) of the value to store, or an array of key-value pairs
     * @param mixed $value The value to store, if a single key is provided
     *
     * @return self For method chaining
     */
    public function flash(array|string $key, $value = null) {
        // If a single key-value pair is provided, store it in the flash session
        if (func_num_args() === 1 && is_array($key)) {
            foreach ($key as $k => $v) {
                // Store the key in the flash session
                $flash[] = $k;
                // Store the value in the session
                $_SESSION[$k] = $v;
            }
        } else {
            // Store the key in the flash session
            $flash[] = $key;
            // Store the value in the session
            $_SESSION[$key] = $value;
        }

        // Store the flash session keys in the session
        $_SESSION['_penobit_flash_keys'] = $flash;

        // Return the instance for method chaining
        return $this;
    }

    /**
     * Get a value from the session flash and remove it.
     *
     * @param string $key The key of the value to retrieve
     * @param mixed $default The default value to return if the key is not found (default null)
     *
     * @return mixed The value, or the default value if the key is not found
     */
    public function pull(string $key, $default = null) {
        // Get the value from the session
        $value = $this->get($key, $default);

        // Remove the key from the session
        $this->remove($key);

        // Return the value
        return $value;
    }

    /**
     * Add one or more values to the session, but only if the key does not exist already.
     *
     * @param array|string $key The key(s) of the value(s) to add, or an array of key-value pairs
     * @param mixed $value The value to add, if a single key is provided
     *
     * @return self For method chaining
     */
    public function add(array|string $key, $value = null) {
        // If a single key-value pair is provided, add it to the session only if the key does not exist already
        if (func_num_args() === 1 && is_array($key)) {
            foreach ($key as $k => $v) {
                // Check if the key already exists in the session
                if ($this->has($k)) {
                    // If the key exists, do nothing
                    continue;
                }
                // Add the key-value pair to the session
                $_SESSION[$k] = $v;
            }
        } elseif (!$this->has($key)) {
            // If the key does not exist in the session, add the key-value pair to the session
            $_SESSION[$key] = $value;
        }

        // Return the instance for method chaining
        return $this;
    }

    /**
     * Get all values stored in the session.
     *
     * @return array The array of all values stored in the session
     */
    public function all() {
        return $_SESSION;
    }

    /**
     * Get all keys stored in the session.
     *
     * @return array The array of all keys stored in the session
     */
    public function keys() {
        return array_keys($_SESSION);
    }

    /**
     * Get all values stored in the session.
     *
     * @return array The array of all values stored in the session
     */
    public function values() {
        return array_values($_SESSION);
    }

    /**
     * Count the number of values stored in the session.
     *
     * @return int The number of values stored in the session
     */
    public function count() {
        // Count the number of values in the session
        return count($_SESSION);
    }

    /**
     * Unset all values stored in the session.
     */
    public function clear() {
        // Unset all values in the session
        foreach ($_SESSION as $k => $v) {
            unset($_SESSION[$k]);
        }
    }
}