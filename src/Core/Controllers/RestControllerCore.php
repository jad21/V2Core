<?php

namespace V2\Core\Controllers;

use V2\Core\Http\Request;

class RestControllerCore extends ControllerCore
{
    
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
        $method_http = $request->method;
		$this->setParameters($request->parameters);
        $action_name = "{$method_name}_$method_http";
        if (not(method_exists($controller, $action_name))) {
        	$action_name = $method_name;
        }
        $action_name = strtolower($action_name);
        
        if (method_exists($controller, $action_name)) {
            $controller->__middleware("before", $request);
            $response_str = call_user_func_array(array($controller, $action_name), $arguments_method);
            $controller->__middleware("after", $request);
        } else {
            header('HTTP/1.1 404 Not Found');
            $response_str = [
            	'Unknown request' => join("/", $request->url_elements),
            	'Method' => $method_http,
            	'action_name'=>$action_name
            ];
        }
        return $response_str;
    }
    protected function is_method_http($method)
    {
    	return in_array(strtoupper($method),["POST","DELETE","HEAD","GET","PUT"]);
    }
}
