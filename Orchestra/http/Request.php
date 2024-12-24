<?php

namespace Orchestra\http;

use Orchestra\database\RecordBuilder;

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
     * 
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

    public function get_url()
    {
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * Validates incoming request body.
     *
     * @param array $params
     * @return array|null  // Always returns an array for errors or null on success
     */
    public function validation_rules($params = [])
    {
        $keyCount = 0;
        $dataArray = [];
        $errors = [];

        // Ensure the request is a POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return [
                'message' => 'Cannot validate body, invalid request method',
                'status' => false
            ];
        }

        // Decode the request body (assumes JSON request body)
        $this->decodedData = json_decode($this->encodedData, true);

        // Compare keys from the request with the expected params
        $keyCompare = array_diff_key($params, $this->decodedData);

        // If there are any missing keys, return an error
        if (!empty($keyCompare)) {
            return [
                'message' => 'Missing required parameters: ' . implode(', ', array_keys($keyCompare)),
                'status' => false
            ];
        }

        foreach ($params as $key => $value) {
            $paramVal = $this->decodedData[$key];

            $rules = explode('|', $value);

            foreach ($rules as $rule) {
                switch ($rule) {
                    case 'required':
                        if (empty($paramVal)) {
                            $errors[$key][] = "$key is required";
                        }
                        break;

                    case 'string':
                        if (!is_string($paramVal)) {
                            $errors[$key][] = "$key must be a string";
                        }
                        break;

                    case 'number':
                    case 'integer':
                        if (!is_numeric($paramVal)) {
                            $errors[$key][] = "$key must be a number";
                        }
                        break;

                    case 'boolean':
                        if (!is_bool($paramVal)) {
                            $errors[$key][] = "$key must be a boolean";
                        }
                        break;
                    case 'date':
                        if (!strtotime($paramVal)) {
                            $errors[$key][] = "$key must be a valid date";
                        }
                        break;
                    case strpos($rule, 'exists:') === 0:
                        list(, $table, $column) = explode(':', $rule . '::') + [null, null];
                        if (!$this->checkIfExists($table, $column, $paramVal)) {
                            $errors[$key][] = "$key does not exist in $table.$column";
                        }
                        break;
                }
            }

            if (empty($errors[$key])) {
                $validated[$key] = $paramVal;
            }
        }

        return array(
            'errors' => $errors,
            'validated' => $validated
        );
    }

    // Helper function to check if a record exists in a database table
    private function checkIfExists($table, $column, $value)
    {
        // This example assumes a database query method like `DB::table($table)->where($column, $value)->exists();`
        // Adjust as needed for your database interaction approach.
        $builder = new RecordBuilder();
        return $builder->from($table)->where($column, '=', $value)->selectFirst();
    }
}
