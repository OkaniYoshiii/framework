<?php

namespace OkaniYoshiii\Framework\Views;

use Exception;
use OkaniYoshiii\Framework\Forms\Form;
use OkaniYoshiii\Framework\Session;

class FormView extends AbstractView
{
    private Form $form;
    private Session $session;

    public function __construct(Form $form)
    {
        $this->form = $form;
        $this->session = Session::getInstance();
    }
    
    private function getStart() : string
    {
        return '<form method="' . $this->form->getMethod() . '" action="' . $this->form->getAction() . '" ' . 'id="' . $this->form->getId() . '" ' . $this->form->getAttributesAsString() . '>';
    }

    private function getEnd() : string
    {
        $view = $this->form->getCsrfInput()->__toString();
        $view .= '</form>';
        return $view;
    }
    
    private function updateCsrfToken() : void
    {
        $this->form->getCsrfInput()->setAttribute('value', $this->session->get('csrf_token'));
    }

    public function __toString(): string
    {
        $this->updateCsrfToken();

        $view = $this->getStart();
        $view .= implode('', $this->form->getInputs());
        $view .= $this->form->getSubmitInput();
        $view  .= $this->getEnd();

        return $view;
    }

    public function render(string $part = 'all') : void
    {
        $possibleValues = ['start', 'all', 'end', 'csrf', 'submit'];

        $this->updateCsrfToken();
        
        echo match($part) {
            'start' => $this->getStart(),
            'all' => $this->__toString(),
            'end' => $this->getEnd(),
            'csrf' => $this->form->getCsrfInput(),
            'submit' => $this->form->getSubmitInput(),
            default => throw new Exception('Argument $part of ' . __METHOD__ . ' is invalid. Possible values are : ' . implode(', ', $possibleValues)),
        };
    }
}