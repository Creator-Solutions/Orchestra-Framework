<?php

namespace Orchestra\http;

use Orchestra\database\RecordBuilder;
use Orchestra\Response;
use app\Models\Sessions;

use Orchestra\logs\LogTypes;
use Orchestra\logs\Logger;

use DateTime;

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

    /**
     * Query parameters from the request URL.
     * @var array
     */
    private array $queryParams;

    protected array $files = [];    

    public function __construct()
    {
        $contentType = '';
        if (isset($_SERVER['CONTENT_TYPE'])) {
            $contentType = $_SERVER['CONTENT_TYPE'];
        } elseif (isset($_SERVER['HTTP_CONTENT_TYPE'])) {
            $contentType = $_SERVER['HTTP_CONTENT_TYPE'];
        }

        $contentType = strtolower(trim(explode(';', $contentType)[0]));
        Logger::write("Raw Content-Type: $contentType", LogTypes::DEBUG);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle JSON first (since it's a raw input)
            if ($contentType === 'application/json') {
                $this->encodedData = file_get_contents('php://input');
                $this->decodedData = json_decode($this->encodedData, true) ?? [];
            }
            // Handle multipart/form-data (with or without files)
            elseif ($contentType === 'multipart/form-data' || !empty($_FILES)) {
                $this->decodedData = $_POST;
                $this->files = $_FILES;
            }
            // Default: regular POST (x-www-form-urlencoded)
            else {
                $this->decodedData = $_POST;
            }
        } else {
            $this->decodedData = $_GET;
        }

        $this->queryParams = $_GET;
        Logger::write("Final files array: " . print_r($_FILES, true), LogTypes::DEBUG);
    }

    public function get($key)
    {
        return $this->decodedData[$key] ?? null;
    }



    /**
     * Retrieve a query parameter value.
     *
     * @param string $key
     * @return string|null
     */
    public function getQueryParam(string $key): ?string
    {
        return $this->queryParams[$key] ?? null;
    }

    /**
     * Retrieve all query parameters.
     *
     * @return array
     */
    public function getQueryParams(): array
    {
        return $this->queryParams;
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

    public function getFile(string $key)
    {
        // Check if the key exists in the files array
        if (!isset($this->files[$key])) {
            Logger::write("Key {$key} not found in files array.", LogTypes::EXCEPTION);  // Log if key is not found
            return null;
        }

        // If multiple files exist
        if (is_array($this->files[$key]['name'])) {
            $fileList = [];
            foreach ($this->files[$key]['name'] as $index => $name) {
                $fileList[] = [
                    'name' => $name,
                    'type' => $this->files[$key]['type'][$index] ?? '',
                    'tmp_name' => $this->files[$key]['tmp_name'][$index] ?? '',
                    'error' => $this->files[$key]['error'][$index] ?? 0,
                    'size' => $this->files[$key]['size'][$index] ?? 0
                ];
            }
            return $fileList;
        }

        // Return a single file as an array
        return [$this->files[$key]]; // Ensure you return the correct structure
    }

    public function getFiles()
    {
        return $this->files;
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
        $validated = [];

        // Ensure the request is a POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return [
                'message' => 'Cannot validate body, invalid request method',
                'status' => false
            ];
        }

        $isMultipart = strpos($_SERVER['CONTENT_TYPE'] ?? '', 'multipart/form-data') !== false;

        // Decode the request body (assumes JSON request body)
        $inputData = $isMultipart
            ? array_merge($_POST, $_FILES) // Combine form fields and files
            : (json_decode($this->encodedData, true) ?? []); // Fallback to JSON


        // Compare keys from the request with the expected params
        $keyCompare = array_diff_key($params, $this->decodedData);

        // If there are any missing keys, return an error
        if (!empty($keyCompare)) {
            return [
                'message' => 'Missing required parameters: ' . implode(', ', array_keys($keyCompare)),
                'status' => false,
                'validated' => (object) $validated, // Always return an object
                'errors' => []
            ];
        }

        foreach ($params as $key => $value) {
            $paramVal = $inputData[$key] ?? null;
            $rules = explode('|', $value);

            if (!in_array('required', $rules) && empty($paramVal)) {
                continue;
            }

            foreach ($rules as $rule) {

                if ($rule === 'image') {
                    if ($isMultipart) {
                        if (!isset($_FILES[$key])) {
                            if (in_array('required', $rules)) {
                                $errors[$key][] = "$key is required";
                            }
                            continue;
                        }

                        if ($_FILES[$key]['error'] !== UPLOAD_ERR_OK) {
                            $errors[$key][] = "$key upload failed with error code: " . $_FILES[$key]['error'];
                            continue;
                        }

                        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                        if (!in_array($_FILES[$key]['type'], $allowedTypes)) {
                            $errors[$key][] = "$key must be a JPEG, PNG, or GIF image";
                        }
                    }
                    continue;
                }


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
                    case "db_bool":
                        if (!in_array($paramVal, [0, 1, "0", "1"], true)) {
                            $errors[$key][] = "$key must be an 1 or 0";
                        }
                        break;
                    case "array":
                        if (!is_array($paramVal)) {
                            $errors[$key][] = "$key must be an array";
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
                    case 'file':
                        if ($isMultipart && (!isset($_FILES[$key]) || $_FILES[$key]['error'] !== UPLOAD_ERR_OK)) {
                            $errors[$key][] = "$key must be a valid file upload";
                        }
                        break;
                    case 'image':
                        if ($isMultipart && isset($_FILES[$key])) {
                            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                            $fileType = $_FILES[$key]['type'];
                            if (!in_array($fileType, $allowedTypes)) {
                                $errors[$key][] = "$key must be an image (JPEG, PNG, GIF)";
                            }
                        }
                        break;

                    case strpos($rule, 'max_size:') === 0:
                        if ($isMultipart && isset($_FILES[$key])) {
                            $maxSize = (int) str_replace('max_size:', '', $rule);
                            if ($_FILES[$key]['size'] > $maxSize * 1024 * 1024) { // Convert MB to bytes
                                $errors[$key][] = "$key must be smaller than $maxSize MB";
                            }
                        }
                        break;
                }

                if (!isset($validated[$key])) {
                    $validated[$key] = null;
                }
            }

            if (empty($errors[$key])) {
                $validated[$key] = $paramVal;
            }
        }

        return [
            'status' => empty($errors),
            'validated' => (object) $validated, // Ensure it's always an object
            'errors' => $errors,
            'message' => empty($errors) ? 'Success' : 'Validation failed'
        ];
    }

    /**
     * Redirects to a given URL with optional query parameters.
     *
     * @param string $url The base URL to redirect to.
     * @param array $params Optional query parameters to append to the URL.
     * @return void
     */
    public function redirect(string $url, array $params = [], array $localStorageData = []): void
    {
        if (!empty($params)) {
            $queryString = http_build_query($params);
            $url .= (strpos($url, '?') === false ? '?' : '&') . $queryString;
        }

        if (!empty($localStorageData)) {
            session_start(); // Start session to temporarily store data
            $_SESSION['localStorageData'] = $localStorageData;
        }

        // Redirect to the helper page, passing the final destination
        header("Location: http://localhost:8000/set-storage?redirect_to=" . urlencode($url));

        exit;
    }

    // Helper function to check if a record exists in a database table
    private function checkIfExists($table, $column, $value)
    {
        // This example assumes a database query method like `DB::table($table)->where($column, $value)->exists();`
        // Adjust as needed for your database interaction approach.
        $builder = new RecordBuilder();
        return $builder->from($table)->where($column, '=', $value)->selectFirst();
    }

    /**
     * Handles checking if the session is currently
     * still valid
     * @return array
     */
    public function validateSession($sessionId): array
    {
        if (empty($sessionId)) {
            return [
                'status' => false,
                'message' => 'Header not found',
                'code' => Response::HTTP_UNAUTHORIZED
            ];
        }

        $session = Sessions::where('session_id', '=', $sessionId)
            ->max('last_activity')
            ->selectFirst(['*']);

        if (empty($session)) {
            return [
                'status' => false,
                'message' => 'Session not found',
                'code' => Response::HTTP_UNAUTHORIZED
            ];
        }

        $lastSessionActivity = new DateTime($session->last_activity);
        $now = new DateTime();

        // Convert the time difference to total minutes
        $totalMinutes = ($lastSessionActivity->diff($now)->days * 24 * 60) +
            ($lastSessionActivity->diff($now)->h * 60) +
            $lastSessionActivity->diff($now)->i;

        if ($totalMinutes > 60) {
            return [
                'status' => false,
                'message' => 'User not authenticated',
                'code' => Response::HTTP_UNAUTHORIZED
            ];
        }

        return [
            'status' => true,
            'session' => $session
        ];
    }

    public function validateFile(string $fieldName, array $options = []): array
    {
        $defaultOptions = [
            'required' => false,
            'max_size' => 2, // 2MB default
            'allowed_types' => ['image/jpeg', 'image/png', 'image/gif'] // Default image types
        ];

        $options = array_merge($defaultOptions, $options);
        $errors = [];

        // Check if file was uploaded
        if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
            if ($options['required']) {
                $errors[] = "File '$fieldName' is required";
            }
            return [
                'valid' => empty($errors),
                'errors' => $errors,
                'file' => null
            ];
        }

        $file = $_FILES[$fieldName];

        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $uploadErrors = [
                UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize directive',
                UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE directive',
                UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
            ];

            $errors[] = $uploadErrors[$file['error']] ?? 'Unknown upload error';
            return [
                'valid' => false,
                'errors' => $errors,
                'file' => $file
            ];
        }

        // Validate file size
        $maxSizeBytes = $options['max_size'] * 1024 * 1024;
        if ($file['size'] > $maxSizeBytes) {
            $errors[] = sprintf(
                "File '%s' exceeds maximum size of %dMB",
                $fieldName,
                $options['max_size']
            );
        }

        // Validate file type
        if (!empty($options['allowed_types'])) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mimeType, $options['allowed_types'])) {
                $errors[] = sprintf(
                    "File '%s' must be one of these types: %s",
                    $fieldName,
                    implode(', ', $options['allowed_types'])
                );
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'file' => $file
        ];
    }

}
