<?php
namespace V2\Core\Services;

trait Single
{
    private static $instance = null;
    
    public static function build()
    {
    	if (is_null(self::$instance)) {
    		self::$instance = new self;
    	}
    	return self::$instance;
    }
    public static function __callStatic($func, $arg)
    {
    	$self = self::build();
        if (method_exists($self,$func)) {
            return call_user_func_array(array($self, $func), $arg);
        }
        if (method_exists($self,"_".$func)) {
        	return call_user_func_array(array($self, "_".$func), $arg);
        	
        }else {
            throw new \Exception("Method static not exists => {$func} in ".get_class($self), 1);
        }
    }
    
}
