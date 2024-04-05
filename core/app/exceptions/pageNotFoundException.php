<?php

namespace App\Exceptions;

class PageNotFoundException extends PenobitException {
    public function __construct(string $message = 'Page Not Found', int $code = 404, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    public function render() {
        // return a 404 Not Found page
        return response()
            ->setStatusCode(404)
            ->header('Content-Type', 'text/html; charset=utf-8')
            ->view('404.tpl')
            ->send()
        ;
    }
}
