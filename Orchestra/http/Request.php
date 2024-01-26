<?php

namespace Orchestra\http;

/**
 * Class that handles request data
 * 
 * (c) @author Owen Burns
 * 
 * @author Creator-Solutions -> Owen Burns
 * @author Founder-Studios -> Owen Burns
 */
class Request
{

    /**
     * Reads Json Data from Request Body
     * @var string
     */
    private string $encodedData;

    /**
     * Creates Assoc Array to Read key => value items
     * @var array
     */
    private array $decodedData;

    public function __construct()
    {
        /**
         * Read json string from Request body
         */
        $this->encodedData = file_get_contents('php://input');

        /**
         * Converts to an assoc array
         * Reads $req[$key] = $value;
         */
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->decodedData = json_decode($this->encodedData, true);
        } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->decodedData = $_GET;
        }
    }

    public function get($key): string|array
    {
        return $this->decodedData[$key] ?? null;
    }

    public function getHeader(string $key)
    {
        $headers = getallheaders();

        if (!\array_key_exists($key, $headers)) {
            return null;
        } else {
            return $headers[$key];
        }
    }
}
