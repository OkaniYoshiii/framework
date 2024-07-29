<?php

namespace App\HTMLElements;

use App\Enums\HTMLInputType;
use App\Enums\HttpMethod;

class HTMLFormElement extends HTMLElement
{
    private const METHOD = HttpMethod::POST->name;
    private const ACTION = '';
    private array $inputs;
    private HTMLInputElement $csrfToken;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->csrfToken = new HTMLInputElement('csrf_token', HTMLInputType::HIDDEN, null, ['value' => 1235]);
    }

    public function addChild(HTMLElement $htmlElement): HTMLElement
    {
        parent::addChild($htmlElement);

        if($htmlElement instanceof HTMLInputElement) {
            $name = $htmlElement->getName();
            $this->inputs[$name] = $htmlElement;
        }

        return $this;
    }

    public function addInput(string $name, HTMLInputType $type, string $label, array $attributes = []) : self
    {
        $this->inputs[$name] = new HTMLInputElement($name, $type, $label, $attributes);

        return $this;
    }

    public function removeInput(string $name) : self
    {
        $childKey = array_search($this->inputs[$name], $this->getChildren(), true);
        
        if($childKey !== false) $this->removeChild($childKey);
        unset($this->inputs[$name]);

        return $this;
    }

    public function getInput(string $name) : HTMLInputElement
    {
        return $this->inputs[$name];
    }
 
    public function getAction()
    {
        return self::ACTION;
    }
    
    public function getMethod()
    {
        return self::METHOD;
    }

    public function getInputs()
    {
        return $this->inputs;
    }

    public function getCsrfToken()
    {
        return $this->csrfToken;
    }
}