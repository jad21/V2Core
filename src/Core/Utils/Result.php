<?php
namespace V2\Core\Utils;

class Result
{
    public $code  = "";
    public $msg   = "";
    public $error = false;
    public $data  = [];
    public function __construct()
    {
        if (func_num_args()==4) {
            $this->make(
                $data = func_get_args()[0], 
                $msg = func_get_args()[1], 
                $code = func_get_args()[2], 
                $error = func_get_args()[3]
            );
        }
    }
    public function make($data, $msg, $code, $error){
        $this->data  = $data;
        $this->code  = $code;
        $this->msg   = $msg;
        $this->error = $error;   
    }
    public static function success($data = [], $msg = "", $code = "OK")
    {
        return new self($data, $msg, $code, $error = false);
    }
    public static function error($msg = "", $data = [], $code = "ERROR")
    {
        return new self($data, $msg, $code, $error = true);
    }
    public function isBad($value = '')
    {
        return $this->error == true;
    }
    public function isError($value = '')
    {
        return $this->error == true;
    }
    public function isOk($value = '')
    {
        return $this->error != true;
    }
    public function isGood($value = '')
    {
        return $this->error != true;
    }
    public function getData()
    {
        return $this->data;
    }
    public function setData($arg1,$arg2=null)
    {
        if (is_not_null($arg2)) {
            if(is_array($this->data)){
                $this->data[$arg1] = $arg2;
            }
        }
    }
    public function getMessage()
    {
        return $this->msg;
    }
    public function toArray()
    {
        return [
            "code"  => $this->code,
            "msg"   => $this->msg,
            "error" => $this->error,
            "data"  => $this->data,
        ];
    }
    
    public function toJson()
    {
        return json_encode($this->toArray(),JSON_PRETTY_PRINT);
    }
    public function __tostring()
    {
        return $this->toJson();
    }
}
