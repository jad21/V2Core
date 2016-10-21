<?php
namespace V2\Core\Reflection;

use ReflectionClass;

class ReflectionAnnotationClass extends ReflectionBase
{

    protected $_class_reflection    = null;
    protected $_builded             = false;
    protected $_annotations_methods = [];

    public function __construct($class)
    {
        $this->_class = $class;
        $this->build();
    }

    protected function build()
    {
        if (not($this->_builded)) {
            $this->_class_reflection = new ReflectionClass($this->_class);
            $comment_string          = $this->_class_reflection->getDocComment();
            preg_match_all($this->_pattern, $comment_string, $matches, PREG_PATTERN_ORDER);
            $this->annotations = $matches[0];
            $this->_builded    = true;
        }
    }
    public function listMethods()
    {
        $methods = get_class_methods($this->_class);
        foreach ($methods as $name) {
            $this->_annotations_methods[$name] = $this->getMethod($name);
        }
        return $this->_annotations_methods;
    }
    public function getMethod($name)
    {
        if (!isset($this->_annotations_methods[$name])) {
            $this->_annotations_methods[$name] = new ReflectionMethod($this, $name);
        }
        return $this->_annotations_methods[$name];
    }
}
