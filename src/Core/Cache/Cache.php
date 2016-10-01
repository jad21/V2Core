<?php
namespace V2\Core\Cache;

abstract class Cache
{
    protected static $directory = VAR_DIRECTORY . "cache";
    public static function put($key, $value)
    {
        if (!is_dir(self::$directory)) {
            mkdir(self::$directory);
        }
        $hash = md5($key);
        $file = self::$directory . "/{$hash}.cache";
        // check the bloody file.
        if (!file_exists($file)) {
            $bool = file_put_contents($file, $value);
            return true;
        }
        return false;
    }
    public static function get($key, $default = null)
    {
        $hash = md5($key);
        $file = self::$directory . "/$hash.cache";
        if (file_exists($file)) {
            return file_get_contents($file);
        }
        return $default;
    }
    public static function has($key)
    {
        return self::get($key, false) ? true : false;
    }

}
