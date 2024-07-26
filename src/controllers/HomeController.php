<?php

namespace App\Controllers;

use App\Traits\SingletonTrait;
use Twig\Environment;

class HomeController
{
    use SingletonTrait;
    
    public function index(Environment $twig) 
    {
        echo $twig->render('index.html.twig', []);
    }
}