<?php
namespace V2\Core\Reflection;

class ReflectionBase implements \ArrayAccess
{
	
    protected $annotations  = [];
    protected $_method_name = [];
    protected $_pattern     = "#(@[a-zA-Z]+\s*[a-zA-Z0-9, ()_].*)#";

    public function getAnnotations()
    {

        return $this->annotations;
    }
    public function getAnnotation($name, $default = null)
    {
        if ($this->hasAnnotation($name)) {
            return $this->annotations[$name];
        }
        return $default;
    }
    public function hasAnnotation($name)
    {
        return isset($this->annotations[$name]);
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->annotations[] = $value;
        } else {
            $this->annotations[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->annotations[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->annotations[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->annotations[$offset]) ? $this->annotations[$offset] : null;
    }
}
