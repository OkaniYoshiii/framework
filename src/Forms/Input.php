<?php

namespace OkaniYoshiii\Framework\Forms;

use OkaniYoshiii\Framework\Enums\HTMLInputType;
use OkaniYoshiii\Framework\Views\InputView;

class Input extends HTMLElement
{
    private HTMLInputType $type;
    private string $name;
    private ?Label $label = null;
    protected array $attributes = ['required' => true, 'disabled' => false];

    public function __construct(Form $form, string $name)
    {
        $this->name = $name;
        $this->id = $form->getId() . '_' . $name;
        $this->label = new Label($this);
    }

    public function setAttribute(string $name, string|bool|float $value): static
    {
        return parent::setAttribute($name, $value);
    }

    public function setType(HTMLInputType $type) : self
    {
        $this->type = $type;

        return $this;
    }

    public function getType() : HTMLInputType
    {
        return $this->type;
    }

    public function setName(string $name) : self
    {
        $this->name = $name;

        return $this;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function setLabel(string $label, array $attributes = []) : self
    {
        $this->label = (new Label($this))
            ->setValue($label)
            ->setAttributes($attributes);

        return $this;
    }

    public function getLabel() : Label
    {
        return $this->label;
    }

    public function setIsDisabled(bool $isDisabled) : self
    {
        $this->attributes['disabled'] = $isDisabled;

        return $this;
    } 

    public function getIsDisabled() : bool
    {
        return $this->attributes['disabled'] ?? false;
    }

    public function setIsRequired(bool $isRequired) : self
    {
        $this->attributes['required'] = $isRequired;

        return $this;
    } 

    public function getIsRequired() : bool
    {
        return $this->attributes['required'];
    }

    public function setIsReadOnly(bool $isReadOnly) : self
    {
        $this->attributes['readonly'] = $isReadOnly;

        return $this;
    } 
}