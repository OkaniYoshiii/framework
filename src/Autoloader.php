<?php

namespace App;

use Exception;

class Autoloader
{
    private static self $instance;

    private function __construct()
    {
        spl_autoload_register(function(string $class) {
            if(!str_starts_with($class, 'App')) throw new Exception('Can\'t load class ' . $class . ' : namespace doesn\'t start with \'App\'');
            $classStr = substr_replace($class, 'src', 0, strlen('app'));
            
            $pos = strrpos($classStr, '\\');
            $className = substr($classStr, $pos);

            $classStr = strtolower($classStr);
            $classStr = substr_replace($classStr, $className, $pos);

            $filepath = '..\\';
            $filepath .= str_replace('\\', DIRECTORY_SEPARATOR, $classStr);
            $filepath .= '.php';

            if(!file_exists($filepath)) throw new Exception('Class ' . $class . ' not found in ' . $filepath . '. The filename doesn\'t match the name of the class or the namespace doesn\'t match the folder structure.');
            require_once $filepath;
        });
    }

    public static function getInstance() : self
    {
        if(!isset(self::$instance)) self::$instance = new Autoloader();

        return self::$instance;
    }
}