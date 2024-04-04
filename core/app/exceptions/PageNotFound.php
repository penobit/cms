<?php

namespace Penobit\App\Exceptions;

use Penobit\App\Http\Response;

class PageNotFound extends \Exception {
    public function __construct(string $message = 'Page Not Found', int $code = 404, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    public function render() {
        // return a 404 Not Found page
        $response = new Response();
        $response->setStatusCode(404);
        $response->header('Content-Type', 'text/html; charset=utf-8');
        $response->write($response->getView()->render('errors/404'));

        return $response;
    }
}
