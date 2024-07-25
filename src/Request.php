<?php

namespace App;

class Request
{
    private string $method;
    private string $path;
    private string $queryString;
    private array $parameters;

    private static self $instance;

    private function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->queryString = $_SERVER['QUERY_STRING'];
        $this->parameters = ($this->method === 'GET') ? $_GET : null;
    }

    public static function getInstance() : self
    {
        if(!isset(self::$instance)) self::$instance = new Request();

        return self::$instance;
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