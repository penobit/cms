<?php

namespace App;

/**
 * Class UrlGenerator.
 *
 * This class generates URLs based on a base URL and a given path.
 */
class UrlGenerator {
    /**
     * The base URL for generating URLs.
     *
     * @var string
     */
    private $baseUrl;

    /**
     * Constructs a new UrlGenerator instance.
     *
     * @param null|string $baseUrl The base URL for generating URLs. If null, uses the current request scheme and host.
     */
    public function __construct(string $baseUrl = null) {
        // Set the base URL to the current request scheme and host if not provided
        $this->baseUrl = $baseUrl ?: $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'];
    }

    /**
     * Generates a URL based on the base URL and the given path.
     *
     * @param string $path the path to append to the base URL
     *
     * @return string the generated URL
     */
    public function url(string $path): string {
        // Generate the URL by appending the given path to the base URL, and trimming leading and trailing slashes
        return sprintf('%s/%s', $this->baseUrl, trim($path, '/'));
    }
}

