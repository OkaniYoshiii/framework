<?php

declare(strict_types=1);

namespace App\Controllers;

use Framework\Contracts\Traits\SingletonTrait;
use Framework\Controllers\Controller;
use Framework\Types\Composed\Request;

class HomeController extends Controller
{
    use SingletonTrait;

    private Request $request;

    public function __construct()
    {
        $this->request = Request::getInstance();
        parent::__construct();
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