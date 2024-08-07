<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Enums\HTMLInputType;
use App\FormValidation;
use App\HTMLElements\HTMLFormElement;
use App\HTMLElements\HTMLInputElement;
use App\Request;
use App\Traits\SingletonTrait;
use App\Validation;

class HomeController extends Controller
{
    use SingletonTrait;

    private Request $request;

    public function __construct()
    {
        $this->request = Request::getInstance();
        parent::__construct();
    }

    public function index() 
    {
        $form = new HTMLFormElement(['novalidate' => 'test', 'data-test' => 'bonjour']);
        $form
            ->setAttribute('novalidate', '')
            ->setAttribute('class', 'bonjour')
            // ->addChild(new HTMLInputElement('name', HTMLInputType::DATE, null))
            ->addInput('email', HTMLInputType::EMAIL, 'Email')
            ->addInput('password', HTMLInputType::PASSWORD, 'Mot de passe')
            ->addInput('submit', HTMLInputType::SUBMIT, 'Envoyer');
        $forms[] = $form;

        $validation = new FormValidation($form);
        if($validation->isSuccessful()) {
            echo 'Bonjour';
            die();
        } else {
            echo implode(', ', $validation->getErrors());
        }

        $form = new HTMLFormElement();
        $form
            ->setAttribute('novalidate', '')
            ->addInput('firstname', HTMLInputType::DATE, 'Date de passage')
            ->addInput('submit', HTMLInputType::SUBMIT, 'Envoyer');

        $forms[] = $form;

        $validation = new FormValidation($form);
        if($validation->isSuccessful()) {
            echo 'Bonjour';
            die();
        } else {
            echo implode(', ', $validation->getErrors());
        }

        echo $this->twig->render('index.html.twig', ['forms' => $forms]);
    }
}