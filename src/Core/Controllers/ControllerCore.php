<?php

namespace V2\Core\Controllers;
use V2\Core\Views\View;
use V2\Core\Http\Request;
class ControllerCore
{
	protected $parameters = [];

	public function __construct()
	{
		# code...
	}

	/**
     *      logica de despachar la ruta
     *    @author Jose Angel Delgado <esojangel@gmail.com>
     *    @param Request $request
     *    @param string $method_name
     *    @param array $arguments_method
     *    @return mixed response
     */
    public function __makeDispatcher__(Request $request, $method_name, $arguments_method = [])
    {
        $controller = $this;
        $this->setParameters($request->parameters);
        $action_name = strtolower($request->method);
        $action_name = strtolower($method_name);
        if (method_exists($controller, $action_name)) {
            $controller->__middleware("before", $request);
            $response_str = call_user_func_array(array($controller, $action_name), $arguments_method);
            $controller->__middleware("after", $request);
        } else {
            header('HTTP/1.1 404 Not Found');
            $response_str = 'Unknown request: ' . join("/", $request->url_elements);
        }
        return $response_str;

    }	

	private static $view = null;
	
	public function view($view,$data=null)
	{
		if (!self::$view) {
			self::$view =  new View();
		}
		self::$view->setTemplate($view,$data);
		return self::$view;
	}
	public function fail($value)
	{
		header('HTTP/1.1 500 Error Server');
		return $value;
	}
	public final function __middleware($name,Request $request)
	{
		if (method_exists($this, $name)) {
			$this->{$name}($request);
		}
	}

	protected function before($request) {}
	protected function after($request) {}

	public function setParameters($parameters)
	{
		$this->parameters = $parameters;
	}
	public function getParameters()
	{
		return $this->parameters;	
	}
	public function param($key=false,$default=null)
	{
		if ($key==false) {
			return $this->parameters;	
		}
		if (isset($this->parameters[$key])) {
			return $this->parameters[$key];
		}
		return $default;
	}
}