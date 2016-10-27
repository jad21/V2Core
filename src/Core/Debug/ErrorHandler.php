
<?php

use V2\Core\Logs\Logger;
use V2\Core\Utils\Result;

class ErrorHandler extends Exception
{
    protected $severity;
    protected $code_error;
    public function __construct($message,$code_error = "ERROREXCEPTIONFATAL",$code = 1, Exception $previous = null,array $data = [])
    {
        $this->message  = $message;
        $this->code_error  = $code_error;
        $this->code     = $code;
        $this->previous     = $previous;
        $this->data     = $data;
    }
    public function setSeverity($value)
    {
        $this->severity = $value;
    }
    public function setFile($value)
    {
        $this->file = $value;
    }
    public function setLine($value)
    {
        $this->line = $value;
    }
    public function setData($key,$value=null)
    {
        if (is_null($value)) {
            $this->data = $key;
        }else{
            if (is_array($this->data)) {
                $this->data[$key] = $value;
            }
        }
        return $this;
    }
    public function getData()
    {
        return $this->data;
    }
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }
    public function setCodeError($code_error)
    {
        $this->code_error = $code_error;
        return $this;
    }
    public function getCodeError()
    {
        return $this->code_error;
    }
    public function isNotNullCodeError()
    {
        return !is_null($this->code_error);
    }
    
    public function getSeverity()
    {
        return $this->severity;
    }
    public static function register()
    {
        set_error_handler("ErrorHandler::exception_error_handler", E_ALL);        
        set_exception_handler("ErrorHandler::exception_handler");
    }
    public static function exception_error_handler($errno, $errstr, $errfile, $errline)
    {
        $e = new self($errstr);   
        $e->setSeverity($errno);
        $e->setFile($errfile);
        $e->setLine($errline);
        throw $e;
    }
    public static function exception_handler($e)
    {
        self::render($e);
    }
    public static function render($e)
    {
        if (php_sapi_name() != "cli") {
	        if(function_exists('header')){
	            header('HTTP/1.1 500 Error Server');
	            header('Content-Type: application/json');
	        }
        }
        $body_exception =
            $e->getMessage() . " " .
            $e->getFile() . ":" . $e->getLine() . " \n" .
            $e->getTraceAsString();
        $code = "ERROREXCEPTIONFATAL::". get_class($e);
        if ($e instanceof self AND $e->isNotNullCodeError()) {
            $code = $e->getCodeError();
        }
        $response_str = Result::error(
            $e->getMessage(),
            [ 
                "file"=>$e->getFile() . ":" . $e->getLine(),
                "trace"=>explode(PHP_EOL,$body_exception),
            ],
            $code
        );
        if ($e instanceof self) {
            $data = $e->getData();
            if (!empty($data)) {
                $response_str->setData("data",$data);
            }
        }
        echo (string)$response_str;
        Logger::error($body_exception,"exception");
        die();
    }

}
/*short name*/
class Err extends \ErrorHandler {}


if (!function_exists('ErrorHandlerFaltal')) {
    /**
     *      helpers para manejar los errores fatales
     *    @author Jose Angel Delgado <esojangel@gmail.com>
     *    @param Exception $e
     */
    function ErrorHandlerFaltal($e)
    {
        ErrorHandler::render($e);
    }
}

ErrorHandler::register();