<?php

declare(strict_types=1);

namespace App\Controllers;

use Framework\Services\FormValidation;
use Framework\Contracts\Traits\SingletonTrait;
use Framework\Controllers\Controller;
use Framework\Enums\HTMLInputType;
use Framework\Types\Composed\HTMLElements\HTMLFormElement;
use Framework\Types\Composed\HTMLElements\HTMLLabelElement;
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
        $form = new HTMLFormElement(['novalidate' => 'test', 'data-test' => 'bonjour']);
        $form
            ->setAttribute('novalidate', '')
            ->setAttribute('class', 'bonjour')
            // ->addChild(new HTMLInputElement('name', HTMLInputType::DATE, null))
            ->addInput('email', HTMLInputType::EMAIL, new HTMLLabelElement('Email'), ['class' => 'bonjour'])
            ->addInput('password', HTMLInputType::PASSWORD, new HTMLLabelElement('Mot de passe'), ['class' => 'bonjour'])
            ->addInput('submit', HTMLInputType::SUBMIT, new HTMLLabelElement('Envoyer'), ['class' => 'bonjour']);
        $forms[] = $form;

        $validation = new FormValidation($form);
        if($validation->isSuccessful()) {
            
        } else {
            echo implode(', ', $validation->getErrors());
        }

        $form = new HTMLFormElement();
        $form
            ->setAttribute('novalidate', '')
            ->addInput('firstname', HTMLInputType::DATE)
            ->addInput('submit', HTMLInputType::SUBMIT);

        $forms[] = $form;

        $validation = new FormValidation($form);
        if($validation->isSuccessful()) {
            echo 'Bonjour';
            die();
        } else {
            echo implode(', ', $validation->getErrors());
        }

        return ['template' => 'index.html.twig', 'variables' => ['forms' => $forms]];
    }
}