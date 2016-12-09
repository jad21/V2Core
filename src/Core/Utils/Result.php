<?php
namespace V2\Core\Utils;
use ArrayAccess;

class Result implements ArrayAccess 
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
    public static function success($data = [], $msg = "success", $code = "OK")
    {
        return new self($data, $msg, $code, $error = false);
    }
    public static function error($msg = "error", $data = [], $code = "ERROR")
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
    public function __get($name)
    {
        if (is_array($this->data)) {
            if (array_key_exists($name, $this->data)) {
                return $this->data[$name];
            }
        }
        if (is_object($this->data)) {
            if (property_exists($this->data,$name)) {
                return $this->data->{$name};
            }
        }
        if (property_exists($this,$name)) {
            return $this->{$name};
        }
        throw new \Exception("data not has: {$name}", 1);
    }
    public function __isset($name)
    {
        if(is_array($this->data)) {
            return isset($this->data[$name]);
        }
        if (is_object($this->data)) {
            return isset($this->data->{$name});
        }
    }
    public function __set($name,$value=null)
    {
        if(is_array($this->data)) {
            $this->data[$name] = $value;
        }
        if (is_object($this->data)) {
            $this->data->{$name} = $value;;
        }
        return $this;
    }

    public function getMessage()
    {
        return $this->msg;
    }
    public function setMessage($msg)
    {
        $this->msg = $msg;
        return $this;
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
    public function getCode()
    {
        return $this->code;
    }
    public function toJson()
    {
        return json_encode($this->toArray(),JSON_PRETTY_PRINT);
    }
    public function __toString()
    {
        return $this->getMessage();
    }

    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return isset($this->data[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->data[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }
}
