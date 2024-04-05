<?php

namespace App;

class UrlGenerator {
    private $baseUrl;

    public function __construct(string $baseUrl = null) {
        $this->baseUrl = $baseUrl ?: $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'];
    }

    public function url(string $path): string {
        return sprintf('%s/%s', $this->baseUrl, trim($path, '/'));
    }
}
