<?php

namespace Orchestra;

include_once('Response.php');

/**
 * Class that handles JsonResponses
 */ 
class JsonResponse extends Response{

    /**
     * @var mixed
     */
    private mixed $content;

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


    public function json(mixed $data = null, int $status, $headers = []){
        parent::setStatusCode($status);
        
        if ($status !== 200){
            if ($data === null){
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