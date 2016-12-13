
<?php

use V2\Core\Logs\Logger;
use V2\Core\Utils\Result;

class ErrorHandler extends Exception
{
    protected $severity;
    protected $code_error;
    public function __construct($message, $code_error = "ERROREXCEPTIONFATAL", $code = 1, Exception $previous = null, array $data = [])
    {
        if ($message instanceof Result) {
            $result_err = $message;
            if ($message->isBad()) {
                $message = $result_err->getMessage();
                if (not(empty($result_err->getCode()))) {
                    $code_error = $result_err->getCode();
                }
                $data = $result_err->getData();
            } else {
                throw new Exception("Error arguments for __construct of ErrorHandler", -1);
            }
        }
        $this->message    = $message;
        $this->code_error = $code_error;
        $this->code       = $code;
        $this->previous   = $previous;
        $this->data       = $data;
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
    public function setData($key, $value = null)
    {
        if (is_null($value)) {
            $this->data = $key;
        } else {
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
        set_error_handler(__CLASS__ . "::exception_error_handler", E_ALL);
        set_exception_handler(__CLASS__ . "::exception_handler");
        register_shutdown_function(__CLASS__ . "::fatalErrorShutdownHandler");
    }
    public static function fatalErrorShutdownHandler()
    {
        $array_error = [E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING, E_STRICT];
        $last_error  = error_get_last();
        foreach ($array_error as $code_error) {
            if ($last_error['type'] == $code_error) {
                // fatal error
                $e = new self($last_error['message']);
                $e->setSeverity($code_error);
                $e->setFile($last_error['file']);
                $e->setLine($last_error['line']);
                self::render($e);
                die();
            }
        }
    }
    public static function exception_error_handler($errno, $errstr, $errfile, $errline)
    {
        $e = new self($errstr);
        $e->setSeverity($errno);
        $e->setFile($errfile);
        $e->setLine($errline);
        self::exception_handler($e);
    }
    public static function exception_handler($e)
    {
        self::render($e);
    }
    public static function render($e)
    {
        if (php_sapi_name() != "cli") {
            if (function_exists('header')) {
                header('HTTP/1.1 500 Error Server');
                header('Content-Type: application/json');
            }
        }
        $body_exception = sprintf("%s %s:%s\n%s",$e->getMessage(),$e->getFile(),$e->getLine(),$e->getTraceAsString());
        
        $code = "ERROREXCEPTIONFATAL::" . get_class($e);
        if ($e instanceof self and $e->isNotNullCodeError()) {
            $code = $e->getCodeError();
        }
        $response_str = Result::error(
            $e->getMessage(),
            [
                "file"  => $e->getFile() . ":" . $e->getLine(),
                "trace" => explode(PHP_EOL, $body_exception),
            ],
            $code
        );
        if (method_exists($e, "getData")) {
            $data = $e->getData();
            if (!empty($data)) {
                $response_str->setData("data", $data);
            }
        }
        echo (string) $response_str->toJson();
        Logger::error($body_exception, "exceptions");
        die();
    }

}
/*short name*/
class Err extends \ErrorHandler
{}

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