<?php

namespace Framework;

use Exception;
use Framework\Contracts\Traits\SingletonTrait;
use Framework\Enums\HttpMethod;
use Framework\Types\Composed\Request;
use Framework\Types\Composed\Route;

class Router
{
    use SingletonTrait;
    
    private Request  $request;
    private array $routes;

    private function __construct()
    {
        $this->request = Request::getInstance();

        $this->decodeRoutes();
    }

    private function decodeRoutes() : void
    {
        match($this->request->getMethod()) {
            HttpMethod::GET->name => $this->routes = json_decode(file_get_contents('../config/routes/routes.get.json'), true),
            HttpMethod::POST->name => $this->routes = json_decode(file_get_contents('../config/routes/routes.post.json'), true),
            default => throw new Exception('Request method ' . $this->request->getMethod() . ' has no routes defined.')
        };
    }

    public function getRoute(string $path = null) : Route
    {
        $path = (is_null($path)) ? $this->request->getPath() : $path;
        return new Route(path : $path, controller : $this->routes[$path]);
    }
}