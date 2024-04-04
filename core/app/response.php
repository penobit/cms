<?php

namespace App;

/**
 * Class Response represents an HTTP response.
 */
class Response {
    /**
     * The HTTP status code of the response.
     *
     * @var int
     */
    private $statusCode;

    /**
     * The response headers.
     *
     * @var array
     */
    private $headers;

    /**
     * The response body.
     *
     * @var string
     */
    private $body;

    /**
     * Constructs a new Response instance.
     *
     * @param int $statusCode the HTTP status code of the response
     * @param array $headers the response headers
     * @param string $body the response body
     */
    public function __construct(string $body = '', int $statusCode = 200, array $headers = []) {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->body = $body;
    }

    /**
     * Sends the response to the client.
     */
    public function send() {
        http_response_code($this->statusCode);

        foreach ($this->headers as $name => $value) {
            header($name.': '.$value);
        }

        echo $this->body;
    }

    /**
     * Sets the response body to a JSON representation of the provided data.
     *
     * @param mixed $data the data to be converted to JSON
     */
    public function json($data) {
        // Set the Content-Type header to indicate JSON response
        $this->headers['Content-Type'] = 'application/json';

        // Convert the provided data to JSON and set it as the response body
        $this->body = json_encode($data);
    }

    /**
     * Merges the provided headers with the existing headers.
     *
     * @param array $headers the headers to merge
     *
     * @return self for method chaining
     */
    public function withHeaders(array $headers) {
        // Merge the provided headers with the existing headers
        $this->headers = array_merge($this->headers, $headers);

        // Allow method chaining
        return $this;
    }

    /**
     * Adds or updates a header.
     *
     * @param string $name the name of the header
     * @param string $value the value of the header
     *
     * @return self for method chaining
     */
    public function header($name, $value) {
        // Add or update a header
        $this->headers[$name] = $value;

        // Allow method chaining
        return $this;
    }

    /**
     * Alias for header() method.
     *
     * @link header()
     */
    public function addHeader($name, $value) {
        return $this->header($name, $value);
    }

    /**
     * Sets the HTTP status code.
     *
     * @param int $statusCode the HTTP status code
     *
     * @return self for method chaining
     */
    public function setStatusCode(int $statusCode) {
        // Sets the HTTP status code
        $this->statusCode = $statusCode;

        // Allow method chaining
        return $this;
    }

    /**
     * Sets the response body.
     *
     * @param string $body the response body
     *
     * @return self for method chaining
     */
    public function setBody(string $body) {
        // Sets the response body
        $this->body = $body;

        // Allow method chaining
        return $this;
    }

    /**
     * Alias for setBody() method.
     *
     * @link setBody()
     */
    public function content(string $content) {
        // Alias for setBody() method
        return $this->setBody($content);
    }

    /**
     * Gets the HTTP status code.
     *
     * @return int the HTTP status code
     */
    public function getStatusCode() {
        // Gets the HTTP status code
        return $this->statusCode;
    }

    /**
     * Gets the response body.
     *
     * @return string the response body
     */
    public function getBody() {
        // Gets the response body
        return $this->body;
    }

    /**
     * Gets all the headers.
     *
     * @return array the headers
     */
    public function getHeaders() {
        // Gets all the headers
        return $this->headers;
    }

    /**
     * Gets a specific header.
     *
     * @param string $name the name of the header
     *
     * @return null|mixed the value of the header or null if not found
     */
    public function getHeader($name) {
        // Gets a specific header
        return $this->headers[$name] ?? null;
    }

    /**
     * Adds or updates a header.
     *
     * @param string $name the name of the header
     * @param mixed $value the value of the header
     *
     * @return self for method chaining
     */
    public function setHeader($name, $value) {
        // Adds or updates a header
        return $this->header($name, $value);
    }
}

