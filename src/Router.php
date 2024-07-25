<?php

namespace App;

use App\Containers\Route;
use App\Request;
use App\Traits\SingletonTrait;
use Exception;

class Router
{
    use SingletonTrait;
    
    private Request $request;
    private array $routes;

    private function __construct()
    {
        $this->request = Request::getInstance();

        $this->decodeRoutes();
    }

    private function decodeRoutes() : void
    {
        match($this->request->getMethod()) {
            'GET' => $this->routes = json_decode(file_get_contents('../config/routes/routes.get.json'), true),
            'POST' => $this->routes = json_decode(file_get_contents('../config/routes/routes.post.json'), true),
            default => throw new Exception('Request method ' . $this->request->getMethod() . ' has no routes defined.')
        };
    }

    public function getRoute(string $path = null) : Route
    {
        $path = (is_null($path)) ? $this->request->getPath() : $path;
        return new Route(path : $path, controller : $this->routes[$path]);
    }
}