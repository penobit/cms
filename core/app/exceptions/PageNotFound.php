<?php

namespace App\Exceptions;

class PageNotFound extends PenobitException {
    public function __construct(string $message = 'Page Not Found', int $code = 404, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    public function getHtml() {
        return sprintf('
            <html>
                <body>
                    <h1>Page Not Found</h1>
                    <p>The requested page was not found.</p>
                    <footer>&copy; %s <a href="https://penobit.com">Penobit</a> </footer>
                </body>
            </html>
        ', persianDate()->format('Y'));
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
