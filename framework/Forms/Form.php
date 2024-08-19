<?php

declare(strict_types=1);

namespace Framework\Forms;

use DateTime;
use Exception;
use Framework\Enums\HTMLInputType;
use Framework\Enums\HTTPMethod;
use Framework\Session;
use Framework\Types\Request;
use Framework\Types\Route;
use Framework\Views\FormView;

final class Form extends HTMLElement
{
    private string $method = HTTPMethod::POST->value;
    private string $action = '';
    private array $data = [];
    private array $errors = [];
    private ?bool $isValidated = null;
    // private array $customValidations = [];

    private array $inputs = [];
    private Input $csrfInput;
    private Input $submitInput;

    private Request $request;
    private Session $session;

    public function __construct(string $id)
    {
        $this->id = $id;
        $this->view = new FormView($this);

        $this->request = Request::getInstance();
        $this->session = Session::getInstance();

        $this->csrfInput = (new Input($this, 'csrf_token'))
            ->setType(HTMLInputType::HIDDEN)
            ->setAttribute('value', $this->session->get('csrf_token'))
            ->setAttributes([]);

        $this->submitInput = (new Input($this, 'submit'))
            ->setType(HTMLInputType::SUBMIT)
            ->setAttributes(['value' => 'Envoyer']);
    }

    public function initializeView() : void
    {
        $this->view = new FormView($this);
    }   

    public function addInput(string $name, HTMLInputType $type, array $attributes = [], bool $required = true) : self
    {
        if(isset($this->inputs[$name])) throw new Exception('Cannot add input to Form : Input with $name "' . $name . '" already exists');

        return $this->setInput($name, $type, $attributes, $required);
    }

    public function addLabel(string $inputName, string $label, array $attributes = []) : self
    {
        if(!isset($this->inputs[$inputName])) throw new Exception('Cannot add label to input : Input with name ' . $inputName . ' does not exists');

        $this->inputs[$inputName]->setLabel($label, $attributes);
        
        return $this;
    }

    public function setSubmitInput(string $value, array $attributes = []) : self
    {
        $this->submitInput = (new Input($this, 'submit'))
            ->setType(HTMLInputType::SUBMIT)
            ->setAttributes($attributes)
            ->setAttribute('value', $value);

        return $this;
    }

    public function getSubmitInput() : Input
    {
        return $this->submitInput;
    }

    public function isSubmitted() : bool
    {
        if(!empty($this->data)) return true;

        if($this->request->getMethod() !== $this->method) return false; 

        // Vérifier que tout les inputs n'étant pas en disabled ont une valeur associée
        foreach($this->inputs as $input)
        {
            if($input->getIsDisabled()) continue;

            $name = $input->getName();

            if(!isset($_POST[$name])) return false;
            $this->data[$name] = $_POST[$name];
        }

        return true;
    }
    
    public function isValidated() : bool
    {
        if(is_null($this->isValidated)) $this->validate();

        return $this->isValidated;
    }

    private function validate() : void
    {
        if(!$this->isSubmitted()) {
            $this->isValidated = false;
            return;
        }
        // if(!$this->isSubmitted()) throw new Exception('Cannot validate ' . $this::class .  ' : form has not been submitted');

        if(!isset($_POST['csrf_token'])) {
            $this->errors[] = 'Le jeton CSRF n\'a pas été envoyé avec le formulaire';
            return;
        }

        if($this->session->get('csrf_token') !== $_POST['csrf_token']) $this->errors[] = 'Le jeton CSRF est invalide';

        foreach($this->inputs as $input)
        {
            $name = $input->getName();
            $type = $input->getType();
            $label = $input->getLabel();
            $attributes = $input->getAttributes();
            $defaultValue =  $attributes['value'] ?? '';

            $isRequired = $attributes['required'] ?? false;
            $isReadOnly = $attributes['readonly'] ?? false;
            // $isDisabled = $attributes['disabled'] ?? false;

            $inputValue = $this->data[$name];

            if($isRequired === true && empty($inputValue)) {
                $this->errors[] = 'Le champ ' . $label->getValue() . ' est requis et ne doit pas être vide';

                continue;
            }

            if($isReadOnly === true && ($defaultValue !== $inputValue)) {
                $this->errors[] = 'Le champ ' . $label->getValue() . ' ne peut pas avoir une valeur différente que celle définie par défaut';
                
                continue;
            }

            if(HTMLInputType::TEXT === $type || HTMLInputType::PASSWORD === $type) {
                $minLength = $attributes['minlength'] ?? null;
                $maxLength = $attributes['maxlength'] ?? null;

                if(!is_string($inputValue)) $this->errors[] = 'Le champ ' . $label->getValue() . ' doit être une chaine de carcatères';
                if(isset($minLength) && strlen($inputValue) < $minLength) $this->errors[] = 'Le champ ' . $label->getValue() . ' doit contenir à minima ' . $minLength . ' caractères';
                if(isset($maxLength) && strlen($inputValue) > $maxLength) $this->errors[] = 'Le champ ' . $label->getValue() . ' doit contenir au maximum ' . $maxLength . ' caractères';

                continue;
            }

            if(HTMLInputType::DATE === $type) {
                $date = DateTime::createFromFormat('Y-m-d', $inputValue);
                if(is_null($date)) $this->errors[] = 'Le champ ' . $label->getValue() . ' est incorrect';
                
                continue;
            }

            if(HTMLInputType::DATETIME_LOCAL === $type) {
                $date = DateTime::createFromFormat('Y-m-d H:i:s', $inputValue);
                if(is_null($date)) $this->errors[] = 'Le champ ' . $label->getValue() . ' est incorrect';
                
                continue;
            }

            if(HTMLInputType::EMAIL === $type) {
                if(!filter_var($inputValue, FILTER_VALIDATE_EMAIL)) $this->errors[] = 'Le champ ' . $label->getValue() . ' n\'est pas un email valide';
                
                continue;
            }
        }

        $this->isValidated = empty($this->errors);
    }

    public function getInputs() : array
    {
        return $this->inputs;
    }

    public function setInput(string $name, HTMLInputType $type, array $attributes = [], bool $required = true) : self
    {
        $input = (new Input($this, $name))
            ->setType($type)
            ->setName($name)
            ->setAttributes($attributes)
            ->setIsRequired($required);
       
        $this->inputs[$name] = $input;

        return $this;
    }

    public function setMethod(HTTPMethod $method) : self
    {
        $this->method = $method->value;

        return $this;
    }

    public function getMethod() : string
    {
        return $this->method;
    }

    public function setAction(Route $route) : self
    {
        $this->action = $route->getPath();

        return $this;
    }

    public function getAction() : string
    {
        return $this->action;
    }    

    public function getData() : array
    {
        return $this->data;
    }

    public function getErrors() : array
    {
        return $this->errors;
    }

    public function getCsrfInput() : Input
    {
        return $this->csrfInput;
    }
}