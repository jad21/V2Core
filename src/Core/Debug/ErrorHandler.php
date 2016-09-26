<?php
class ErrorHandler extends Exception
{
    protected $severity;

    public function __construct($message, $code, $severity, $filename, $lineno)
    {
        $this->message  = $message;
        $this->code     = $code;
        $this->severity = $severity;
        $this->file     = $filename;
        $this->line     = $lineno;
    }

    public function getSeverity()
    {
        return $this->severity;
    }
    public function register()
    {
        
    }
}

function exception_error_handler($errno, $errstr, $errfile, $errline)
{
    throw new ErrorHandler($errstr, 0, $errno, $errfile, $errline);
}
set_error_handler("exception_error_handler", E_ALL);


use V2\Core\Logs\Logger;
use V2\Core\Utils\Result;
if (!function_exists('ErrorHandlerFaltal')) {
    /**
     *      helpers para manejar los errores fatales
     *    @author Jose Angel Delgado <esojangel@gmail.com>
     *    @param Exception $e
     */
    function ErrorHandlerFaltal($e)
    {
        if(function_exists('header')){
            header('HTTP/1.1 500 Error Server');
            header('Content-Type: application/json');
        }
        $body_exception =
        $e->getMessage() . " " .
        $e->getFile() . ":" . $e->getLine() . " \n" .
        $e->getTraceAsString();
        $response_str = Result::error(
            $e->getMessage(),
            [ "trace"=>explode(PHP_EOL,$body_exception) ],
            $code = "ERROREXCEPTIONFATAL"
            );
        echo $response_str;
        Logger::error($body_exception,"exception");
        exit();
    }
}
set_exception_handler(function ($e) {
    ErrorHandlerFaltal($e);
});
