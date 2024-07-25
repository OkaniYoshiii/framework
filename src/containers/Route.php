<?php

namespace App\Containers;

class Route
{
    private string $path;
    private string $controller;
    private string $method;

    public function __construct(string $path, string $controller)
    {
        $this->path = $path;
        list($this->controller, $this->method) = explode('::', $controller);
    }

    /**
     * Get the value of path
     */ 
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get the value of controller
     */ 
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Get the value of method
     */ 
    public function getMethod()
    {
        return $this->method;
    }
}