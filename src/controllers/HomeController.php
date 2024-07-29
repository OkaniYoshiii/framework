<?php

namespace App\Controllers;

use App\Enums\HTMLInputType;
use App\Enums\HttpMethod;
use App\FormValidator;
use App\HTMLElements\HTMLFormElement;
use App\HTMLElements\HTMLInputElement;
use App\Request;
use App\Traits\SingletonTrait;

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
        $form = new HTMLFormElement();
        $form
            ->addChild(new HTMLInputElement('animal', HTMLInputType::TEXT, 'Label'))
            ->setAttribute('class', 'd-flex');
        $forms[] = $form;
        
        if($this->request->getMethod() === HttpMethod::POST) new FormValidator($form);

        $form = clone $form;
        $form
            ->removeInput('animal')
            ->addInput('firstname', HTMLInputType::TEXT, 'Label');
        $forms[] = $form;

        if($this->request->getMethod() === HttpMethod::POST) new FormValidator($form);

        echo $this->twig->render('index.html.twig', ['forms' => $forms]);
    }
}