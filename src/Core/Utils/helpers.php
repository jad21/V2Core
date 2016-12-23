<?php

use V2\Core\Utils\Collection;
use V2\Core\Utils\Env;
use V2\Core\Utils\Result;
if (!function_exists('data_get')) {
    /**
     * Get an item from an array or object using "dot" notation.
     *
     * @param  mixed   $target
     * @param  string|array  $key
     * @param  mixed   $default
     * @return mixed
     */
    function data_get($target, $key, $default = null)
    {
        if (is_null($key)) {
            return $target;
        }

        $key = is_array($key) ? $key : explode('.', $key);

        foreach ($key as $segment) {
            if (is_array($target)) {
                if (!array_key_exists($segment, $target)) {
                    return value($default);
                }

                $target = $target[$segment];
            } elseif ($target instanceof ArrayAccess) {
                if (!isset($target[$segment])) {
                    return value($default);
                }

                $target = $target[$segment];
            } elseif (is_object($target)) {
                if (!isset($target->{$segment})) {
                    return value($default);
                }

                $target = $target->{$segment};
            } else {
                return value($default);
            }
        }

        return $target;
    }
}

if (!function_exists('dd')) {
    /**
     * Dump the passed variables and end the script.
     *
     * @param  mixed
     * @return void
     */
    function dd()
    {   
        if (php_sapi_name() != "cli") echo "<pre>";
        array_map(function ($x) {
            var_dump($x);
            // (new Dumper)->dump($x);
        }, func_get_args());
        if (php_sapi_name() != "cli") echo "</pre>";
        die(1);
    }
}

if (!function_exists('pp')) {
    /**
     * Dump the passed variables and end the script.
     *
     * @param  mixed
     * @return void
     */
    function pp()
    {   
        if (php_sapi_name() != "cli") echo "<pre>";
        array_map(function ($x) {
            print_r($x);
            // (new Dumper)->dump($x);
        }, func_get_args());
        if (php_sapi_name() != "cli") echo "</pre>";
        die(1);
    }
}
if (!function_exists('d')) {
    /**
     * Dump the passed variables and end the script.
     *
     * @param  mixed
     * @return void
     */
    function d()
    {
        if (php_sapi_name() != "cli") echo "<pre>";
        array_map(function ($x) {
            var_dump($x);
            // (new Dumper)->dump($x);
        }, func_get_args());
        if (php_sapi_name() != "cli") echo "</pre>";
    }
}

if (!function_exists('collect')) {
    /**
     * Dump the passed variables and end the script.
     *
     * @param  mixed
     * @return void
     */
    function collect($array = [])
    {
        return new Collection($array);
    }
}

if (!function_exists('url')) {
    /**
     * Dump the passed variables and end the script.
     *
     * @param  mixed
     * @return void
     */
    function url($url = "")
    {
        if (isset($_SERVER)) {
            
            $http    = isset($_SERVER['REQUEST_SCHEME'])?$_SERVER['REQUEST_SCHEME']:"http";
            $host    = $_SERVER["HTTP_HOST"];
            $urlbase = $_SERVER["REQUEST_URI"];
            $urlbase = parse_url($urlbase)["path"];
            if (isset($_SERVER["PATH_INFO"])) {
                $urlbase = strtr($urlbase, [$_SERVER["PATH_INFO"] => ""]);
                $urlbase = parse_url($urlbase)["path"];
            }
            if ($url != "") {
                $url = ltrim($url);
                $url = rtrim($url);
                if (strlen($url) > 0 and substr($url, 0, 1) == "/") {
                    $url = substr($url, 1);
                }
            }
            return "{$http}://{$host}{$urlbase}{$url}";
        }
        return null;
    }
}

if (!function_exists('object_get')) {
    /**
     * Get an item from an object using "dot" notation.
     *
     * @param  object  $object
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    function object_get($object, $key, $default = null)
    {
        if (is_null($key) || trim($key) == '') {
            return $object;
        }

        foreach (explode('.', $key) as $segment) {
            if (!is_object($object) || !isset($object->{$segment})) {
                return value($default);
            }

            $object = $object->{$segment};
        }

        return $object;
    }
}

if (!function_exists('time_elapsed')) {
    function time_elapsed($secs)
    {
        $bit = array(
            'y' => $secs / 31556926 % 12,
            'w' => $secs / 604800 % 52,
            'd' => $secs / 86400 % 7,
            'h' => $secs / 3600 % 24,
            'm' => $secs / 60 % 60,
            's' => $secs % 60,
        );
        $ret = [];
        foreach ($bit as $k => $v) {
            if ($v > 0) {
                $ret[] = $v . $k;
            }
        }

        return join(' ', $ret);
    }
}
if (!function_exists('is_not_null')) {
    function is_not_null($val)
    {
        return !is_null($val);
    }
}
if (!function_exists('env')) {
    function env($file = null)
    {
        if (defined("ENV")) {
            $name_array = explode(".",$file);
            $extension = array_pop($name_array);
            $file = join(".",$name_array).".".ENV.".".$extension;
        }
        return Env::getData($file);
    }
}
if (!function_exists('etc')) {
    function etc($file = null)
    {
        return Env::getData($file);
    }
}
if (!function_exists('value')) {
    function value($arg)
    {
        return $arg;
    }
}

if (!function_exists('je')) {
    function je($val)
    {
        return json_encode($val);
    }
}
if (!function_exists('jd')) {
    function jd($val)
    {
        return json_decode($val);
    }
}

if (!function_exists('result')) {
    function result()
    {
        return new Result();
    }
}

if (!function_exists('ex')) {
    function ex($arg)
    {
        exit($arg);
    }
}

if (!function_exists('not')) {
    function not($arg)
    {
        return !($arg);
    }
}

if (!function_exists('lower_camel_case')) {
    function lower_camel_case($input)
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }
}
if (!function_exists('upper_camel_case')) {
    function upper_camel_case($str)
    {
        $i   = array("-", "_");
        $str = preg_replace('/([a-z])([A-Z])/', "\\1 \\2", $str);
        $str = preg_replace('@[^a-zA-Z0-9\-_ ]+@', '', $str);
        $str = str_replace($i, ' ', $str);
        $str = str_replace(' ', '', ucwords(strtolower($str)));
        $str = strtolower(substr($str, 0, 1)) . substr($str, 1);
        return $str;
    }
}

if (!function_exists('not_empty')) {
    function not_empty($arg)
    {
        return !empty($arg);
    }
}
