<?php

namespace App\Exceptions;

use App\Response;

/**
 * Class DatabaseQueryException.
 *
 * Exception thrown when a database query error occurs.
 */
class DatabaseQueryException extends PenobitException {
    /**
     * Constructor for the DatabaseQueryException class.
     *
     * @param string $message the error message
     * @param int $code the error code
     * @param null|\Exception $previous the previous exception
     */
    public function __construct(string $message = 'Page Not Found', int $code = 404, ?\Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Render the exception as a HTTP response.
     *
     * @return Response the HTTP response
     */
    public function render() {
        // Create a response with a 500 status code, using the 'error.tpl' view,
        // passing the error message from the parent exception and a title.
        return response()
            ->setStatusCode(500)
            ->view('error.tpl', [
                'title' => 'Database Query Error',
                'message' => parent::getMessage(),
            ])
            ->send()
        ;
    }
}

