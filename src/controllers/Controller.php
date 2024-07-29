<?php

namespace App\Controllers;

use App\Singleton;
use Twig\Environment;

class Controller
{    
    protected Environment $twig;
    
    protected function __construct()
    {
        global $twig;
        $this->twig = $twig;
    }
}