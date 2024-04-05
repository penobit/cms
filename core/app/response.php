<?php

namespace App;

use App\Interfaces\Collection;
use App\Utils\HtmlMinifier;

/**
 * Class Response represents an HTTP response.
 */
class Response {
    /**
     * Flags used when converting to JSON.
     * For example:
     * JSON_PRETTY_PRINT - Use whitespace in the output for readability.
     * JSON_UNESCAPED_SLASHES - Don't put backslashes before characters that
     *   don't need to be escaped in JSON strings.
     * JSON_UNESCAPED_UNICODE - Encode multibyte Unicode characters literally
     *   (default is to escape as \uXXXX).
     *
     * @var int
     */
    public static $jsonResponseFlags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;

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
     */
    private string|Template $body;

    private string $view;

    /**
     * Constructs a new Response instance.
     *
     * @param int $statusCode the HTTP status code of the response
     * @param array $headers the response headers
     * @param string $body the response body
     */
    public function __construct(mixed $body = '', int $statusCode = 200, array $headers = []) {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->body = $this->parseBody($body);
    }

    /**
     * Parses the given body into a JSON string if it's an array,
     * a Collection, or another Response instance.
     *
     * @param mixed $body the response body to parse
     *
     * @return string the parsed body as a JSON string
     */
    public function parseBody($body) {
        // Check if the body is an array
        if (is_array($body)) {
            // Add application/JSON content type header
            $this->addHeader('Content-Type', 'application/json');

            // Convert it to a JSON string using the specified flags
            return json_encode($body, self::$jsonResponseFlags);
        }

        // Check if the body is a Collection instance
        if ($body instanceof Collection) {
            // Add application/JSON content type header
            $this->addHeader('Content-Type', 'application/json');

            // Convert the Collection to a JSON string
            return $body->toJson();
        }

        // Check if the body is another Response instance
        if ($body instanceof self) {
            // Return the body of the Response
            return $body->body;
        }

        // Convert the body to a string and return it
        return (string) $body;
    }

    /**
     * Sends the response to the client.
     */
    public function send() {
        http_response_code($this->statusCode);

        foreach ($this->headers as $name => $value) {
            header($name.': '.$value);
        }

        ob_start();
        echo (string) $this->body;
        $res = ob_get_contents();
        ob_clean();

        if (strpos($this->headers['Content-Type'] ?? '', 'html') !== false) {
            $minifier = new HtmlMinifier();
            echo $minifier->minify($res);
        } else {
            echo $res;
        }
    }

    /**
     * Sets the response body to a JSON representation of the provided data.
     *
     * @param mixed $data the data to be converted to JSON
     *
     * @return self for method chaining
     */
    public function json($data) {
        // Set the Content-Type header to indicate JSON response
        $this->headers['Content-Type'] = 'application/json';

        // Convert the provided data to JSON and set it as the response body
        $this->body = json_encode($data);

        return $this;
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
     * Set a header if it doesn't exist already.
     *
     * @link header()
     */
    public function addHeader($name, $value) {
        $header = $this->getHeader($name);

        if (!isset($header)) {
            $this->header($name, $value);
        }

        return $this;
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

    /**
     * A description of the entire PHP function.
     *
     * @param string $name description
     * @param mixed $data description
     *
     * @return $this
     */
    public function view(string $name, $data = []) {
        $this->view = $name;
        $this->body = view($name, $data);
        $this->setHeader('Content-Type', 'text/html');

        return $this;
    }
}

