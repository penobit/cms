<?php

namespace Penobit\App\Exceptions;

/**
 * Exception thrown when a page is not found.
 */
class PageNotFound extends \Exception {
    /**
     * PageNotFound constructor.
     *
     * @param string $message The Exception message to throw. Default: 'Page Not Found'.
     * @param int $code The Exception code. Default: 404.
     * @param null|\Exception $previous Previous exception used for the chaining. Default: null.
     */
    public function __construct(string $message = 'Page Not Found', int $code = 404, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

