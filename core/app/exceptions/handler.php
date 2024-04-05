<?php

namespace App\Exceptions;

use App\Template;

class Handler {
    public static function handle(\Throwable $exception) {
        if ($exception instanceof PenobitException) {
            return $exception->render();
        }

        $template = new Template('error.tpl', [
            'title' => 'Error',
            'message' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
            'line' => $exception->getLine(),
            'file' => $exception->getFile(),
        ]);

        echo $template->render();
    }
}
