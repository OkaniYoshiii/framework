<?php

declare(strict_types=1);

namespace Framework\Types\Composed\HTMLElements;

use Framework\Enums\HTMLInputType;
use Framework\Enums\HttpMethod;
use Framework\Exceptions\CauseEffectException;
use Framework\Types\Collections\HTMLAttributeCollection;

class HTMLFormElement extends HTMLElement
{
    private const METHOD = HttpMethod::POST->name;
    private const ACTION = '';
    private array $inputs;

    public function __construct(array $attributes = [])
    {
        try {
            $attributes = HTMLAttributeCollection::createFromArray($attributes);
        } catch(CauseEffectException $e) {
            throw new CauseEffectException('Cannot create ' . self::class, $e->getCause());
        }

        parent::__construct($attributes);
        
        $this->addChild(new HTMLInputElement('csrf_token', HTMLInputType::HIDDEN, null, ['value' => '0000']));
    }

    public function addChild(HTMLElement $htmlElement) : self
    {
        parent::addChild($htmlElement);

        if($htmlElement instanceof HTMLInputElement) {
            $name = $htmlElement->getName();
            $this->inputs[$name] = $htmlElement;
        }

        return $this;
    }

    public function setAttribute(string $name, string $value) : self
    {
        return parent::setAttribute($name, $value);
    }

    public function addInput(string $name, HTMLInputType $type, ?string $label = '', ?array $attributes = []) : self
    {
        try {
            $input = new HTMLInputElement($name, $type, $label, $attributes);
        } catch(CauseEffectException $e) {
            throw new CauseEffectException('Cannot add input to ' . self::class, $e->getCause());
        }
        
        $this->childs->add($input);
        $this->inputs[] = $input;

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
}