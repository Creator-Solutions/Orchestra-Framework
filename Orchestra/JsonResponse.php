<?php

namespace Orchestra;

include_once('Response.php');

/**
 * Class that handles JsonResponses
 */
class JsonResponse extends Response
{

    /**
     * @var mixed
     */
    private mixed $data;

    /**
     * Options variable for Json response
     * @var array
     */
    private array $headers;

    /**
     * @var int
     */
    private int $status;

    /**
     * @var string
     */
    private string $statusText;

    public const DEFAULT_ENCODING_OPTIONS = 15;
    protected int $encodingOptions = self::DEFAULT_ENCODING_OPTIONS;

    public function __construct(mixed $data, int $response, $headers = [], bool $json = false)
    {
        $this->status = $response;
        $this->data = $data;

        if ($json && !\is_string($data) && !is_numeric($data) && !\is_callable([$data, '__toString'])) {
            throw new \TypeError(sprintf('"%s": If $json is set to true, argument $data must be a string or object implementing __toString(), "%s" given.', __METHOD__, get_debug_type($data)));
        }

        if ($json && empty($data)) {
            throw new \InvalidArgumentException('"%s": If JSON is set to true an object must be provided');
        }
    }

    public function send()
    {
        // Set headers
        header('Content-Type: application/json');
        http_response_code($this->status);

        // Encode data as JSON and echo
        echo json_encode($this->data);
    }

    public function build(mixed $data, int $code, bool $json)
    {
        if ($json && !empty($data)) {
            return json_encode($data);
        } else {
            return $data;
        }

        return json_encode($data);
    }

    /**
     * Sets a raw string containing a JSON document to be sent.
     *
     * @return $this
     */
    public function setJson(string $json)
    {
        $this->data = $json;

        return $this->data;
    }

    /**
     * Sets the data to be sent as JSON.
     *
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function setData(mixed $data = [])
    {
        try {
            $data = json_encode($data, $this->encodingOptions);
        } catch (\Exception $e) {
            if ('Exception' === $e::class && str_starts_with($e->getMessage(), 'Failed calling ')) {
                throw $e->getPrevious() ?: $e;
            }
            throw $e;
        }

        if (\JSON_THROW_ON_ERROR & $this->encodingOptions) {
            return $this->setJson($data);
        }

        if (\JSON_ERROR_NONE !== json_last_error()) {
            throw new \InvalidArgumentException(json_last_error_msg());
        }

        return $this->setJson($data);
    }

    public function json(mixed $data = null, int $status, $headers = [])
    {
        parent::setStatusCode($status);

        if ($status !== 200) {
            if ($data === null) {
                $this->statusText = parent::getStatusText();

                $data = [
                    'Server Error: ' => $this->statusText
                ];
            }
        }

        $headerKey = 'Content-Type';
        $headerValue = $headers['Content-Type'];

        header("$headerKey: $headerValue");
        http_response_code($status);
        echo json_encode($data);
    }
}
