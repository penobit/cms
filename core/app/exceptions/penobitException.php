<?php

namespace App\Exceptions;

use App\Response;

/**
 * Class PenobitException.
 *
 * Exception thrown when a general Penobit exception occurs.
 */
class PenobitException extends \Exception {
    /**
     * Render the exception as a HTTP response.
     *
     * This method creates a HTTP response with a 500 status code, using the 'error.tpl' view,
     * passing the error message from the parent exception and a title.
     *
     * @return Response the HTTP response
     */
    public function render() {
        // Create a response with a 500 status code, using the 'error.tpl' view,
        // passing the error message from the parent exception and a title.
        return response()
            ->setStatusCode(500)
            ->view('error.tpl', [
                'title' => 'Error',  // The title of the error page.
                'message' => parent::getMessage(),  // The error message from the parent exception.
            ])
            ->send()
        ;
    }
}
