<?php

namespace App\Exceptions;

class DatabaseQueryException extends PenobitException {
    public function __construct(string $message = 'Page Not Found', int $code = 404, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    public function getHtml() {
        return sprintf('
            <html>
                <body>
                    <h1>Database Query Error</h1>
                    <p>%s</p>
                    <footer>&copy; %s <a href="https://penobit.com">Penobit</a> </footer>
                </body>
            </html>
        ', $this->getMessage(), persianDate()->format('Y'));
    }

    public function render() {
        // return a 404 Not Found page
        return response()
            ->setStatusCode(404)
            ->header('Content-Type', 'text/html; charset=utf-8')
            ->content($this->getHtml())
            ->send()
        ;
    }
}
