<?php

declare(strict_types=1);

namespace App\Controllers;

use Framework\Contracts\Traits\SingletonTrait;
use Framework\Types\Request;

class HomeController
{
    use SingletonTrait;

    private Request $request;

    public function __construct()
    {
        $this->request = Request::getInstance();
    }

    public function index() : array
    {
        $form = require '../src/Forms/contact-form.php';

        if($form->isValidated()) {
            echo 'Bonjour';
        }

        return ['template' => 'index.html.twig', 'variables' => ['form' => $form]];
    }
}