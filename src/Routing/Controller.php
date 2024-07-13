<?php

namespace Wpframe\Sys\Routing;

use Wpframe\Sys\Container\Container;

/**
 * Controller Class to handle client requests
 */
class Controller 
{
    /**
     * Call Controller class and resolve
     *
     * @param string|closure $controller
     * @param mixed $method
     * @param array $data
     * @return mixed
     */
    public function dispatcher($controller, $method = null, $data = [])
    {
        return $this->callAction($controller, $method, $data);
    }

    /**
     * Call Class or Closure to resolve
     *
     * @param string|closure $controller
     * @param mixed $method
     * @param array $data
     * @return mixed
     */
    public function callAction($controller, $method = null, $data = [])
    {
        return Container::callAction($controller, $method, $data);
    }
}