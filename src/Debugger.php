<?php

namespace App;

use App\Traits\SingletonTrait;

class Debugger
{
    use SingletonTrait;
    
    public function dump(mixed $value) : void
    {
        echo '<pre>';
        var_dump($value);
        echo '</pre>';
    }
}