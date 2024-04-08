<?php

namespace App;

/**
 * Class Redirect represents a redirect in the application.
 *
 * The Redirect class provides a way to redirect users to a specified path.
 * It can be used to redirect to a specific route or an arbitrary URL.
 */
class Redirect {
    /**
     * Constructs a new Redirect instance.
     *
     * @param null|string $path the path to redirect to
     * @param null|bool $statusCode determines if the redirect is statusCode (HTTP 301)
     */
    public function __construct(
        private ?string $path,
        private int $statusCode = 302,
    ) {}

    /**
     * Get the value of path.
     */
    public function getPath(): ?string {
        return $this->path;
    }

    /**
     * Set the value of path.
     */
    public function setPath(?string $path): self {
        $this->path = $path;

        return $this;
    }

    /**
     * Get the value of statusCode.
     */
    public function getstatusCode(): int {
        return $this->statusCode;
    }

    /**
     * Set the value of statusCode.
     */
    public function setstatusCode(int $statusCode): self {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * Redirects the user to the specified path.
     *
     * @param string $path the path to redirect to
     */
    public static function to(string $path, bool $permanent = false): void {
        // Sets the appropriate HTTP status code for the redirect.
        if ($permanent) {
            header('HTTP/1.1 301 Moved Permanently');
        }

        // Sets the "Location" header to redirect the user.
        header('Location: '.$path);
    }

    /**
     * Redirects the user to the specified route.
     *
     * @param string $route the name of the route to redirect to
     * @param mixed ...$args The arguments to pass to the route.
     */
    public function route(string $route, ...$args): self {
        // Generate the URL for the specified route.
        $url = route($route, ...$args);

        // Set the path of the redirect to the generated URL.
        $this->setPath($url);

        return $this;
    }

    /**
     * Set the redirect to a permanent (301) status code.
     */
    public function permanent(): self {
        $this->statusCode = 301;

        return $this;
    }

    /**
     * Sets the redirect to a temporary (302) status code.
     */
    public function temporary(): self {
        $this->statusCode = 302;

        return $this;
    }
}
