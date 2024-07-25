<?php

namespace App;

use App\Container\Route;
use App\Request;
use Exception;

class Router
{
    private Request $request;

    private array $routes;

    private static self $instance;

    private function __construct()
    {
        $this->request = Request::getInstance();

        $this->decodeRoutes();
    }

    public static function getInstance() : self
    {
        if(!isset(self::$instance)) self::$instance = new Router();
        
        return self::$instance;
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