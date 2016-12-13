<?php
namespace V2\Core\Http;

use Exception;

class ResponseJson
{
    /**
     * Response data.
     *
     * @var string
     */
    protected $data;

    /**
     * Constructor.
     *
     * @param string $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Render the response as JSON.
     *
     * @return string
     */
    public function toJson()
    {
        header('Content-Type: application/json');
        if (is_object($this->data) and method_exists($this->data, "toJson")) {
            return $this->data->toJson();
        } else if (is_object($this->data) or is_array($this->data)) {
            return json_encode($this->data, JSON_PRETTY_PRINT);
        }
        return json_encode(["data" => $this->data], JSON_PRETTY_PRINT);

    }

    public function is_json_valid($value)
    {
        try {
            json_decode($value);
            return (json_last_error() === JSON_ERROR_NONE);
        } catch (Exception $e) {
            return false;
        }
    }

    public function __toString()
    {
        return (string) $this->toJson();
    }
}
