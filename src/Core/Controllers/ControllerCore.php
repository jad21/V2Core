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
}