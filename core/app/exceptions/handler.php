<?php

namespace App\Exceptions;

class Handler {
    public static function handle(\Exception $exception) {
        if ($exception instanceof PenobitException) {
            return $exception->render();
        }

        throw $exception;
    }
}
