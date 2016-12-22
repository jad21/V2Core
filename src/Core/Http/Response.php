<?php
namespace V2\Core\Http;
use V2\Core\Views\View;
class Response
{
    private $result = "";
    /**
     * Constructor.
     *
     * @param string $data
     * @param string $format
     */
    public function __construct($data, $format,$code=200)
    {
        if ($data instanceof View) {
            return $this->result = $data->render();
        }else{
            switch ($format) {
                case 'application/json':
                default:
                    $this->result = new ResponseJson($data);
                break;
            }
        }
        
    }

    public function __toString() {
        
        return (string)$this->result;
    }
}