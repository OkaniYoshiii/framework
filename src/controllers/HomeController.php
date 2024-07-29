<?php

namespace App\Controllers;

use App\Forms\Form;
use App\Traits\SingletonTrait;

class HomeController extends Controller
{
    use SingletonTrait;

    public function __construct()
    {
        parent::__construct();
    }

    public function index() 
    {
        $form = new Form();
        $form
            ->addInput('animal', 'text', 'Label')
            ->addAttribute('class', 'd-flex');
        $forms[] = $form;

        $form = clone $form;
        $form
            ->removeInput('animal')
            ->addInput('firstname', 'text', 'Label');
        $forms[] = $form;
        var_dump($forms);

        echo $this->twig->render('index.html.twig', ['forms' => $forms]);
    }
}