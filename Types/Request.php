<?php

namespace Framework\Types;

use Framework\Contracts\Traits\SingletonTrait;

class Request
{
    use SingletonTrait;
    
    private string $method;
    private string $path;
    private string $queryString;
    private array $parameters;

    private function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->queryString = $_SERVER['QUERY_STRING'];
        $this->parameters = ($this->method === 'GET') ? $_GET : [];
    }

    /**
     * Get the value of method
     */ 
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Get the value of path
     */ 
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get the value of queryString
     */ 
    public function getQueryString()
    {
        return $this->queryString;
    }

    /**
     * Get the value of parameters
     */ 
    public function getParameters()
    {
        return $this->parameters;
    }
}