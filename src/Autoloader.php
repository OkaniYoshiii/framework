<?php

namespace App;

use App\Traits\SingletonTrait;
use Exception;

require_once 'traits/SingletonTrait.php';

class Autoloader
{
    use SingletonTrait;

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

            echo $filepath;
            echo '<br>';

            if(!file_exists($filepath)) throw new Exception('Class ' . $class . ' not found in ' . $filepath . '. The filename doesn\'t match the name of the class or the namespace doesn\'t match the folder structure.');
            require_once $filepath;
        });
    }
}