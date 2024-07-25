<?php

namespace App\Traits;

trait SingletonTrait
{
    protected static self $instance;

    private function __construct() {}

    public static function getInstance() : self
    {
        $class = __CLASS__;
        if(!isset(self::$instance)) self::$instance = new $class();

        return self::$instance;
    }
}