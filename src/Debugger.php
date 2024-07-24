<?php

namespace App;

class Debugger
{
    public function dump(mixed $value) : void
    {
        echo '<pre>';
        var_dump($value);
        echo '</pre>';
    }
}