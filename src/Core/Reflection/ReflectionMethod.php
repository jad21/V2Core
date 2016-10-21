<?php
namespace V2\Core\Reflection;

class ReflectionMethod extends ReflectionBase
{
    protected $_class
    protected $_method_name = [];
    protected $_builded     = false;

    public function __construct(ReflectionAnnotationClass $class, $name)
    {
        $this->_class       = $class;
        $this->_method_name = $name;
        $this->build();
    }

    public function build()
    {
        if (not($this->_builded)) {
            $comment_string = $this->_class->getMethod($this->_method_name)->getDocComment()
            preg_match_all($this->_pattern, $comment_string, $matches, PREG_PATTERN_ORDER);
            $this->annotations = $matches[0];
            $this->_builded    = true;
        }
    }

}
