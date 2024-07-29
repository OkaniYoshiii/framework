<?php

namespace App;

use App\Enums\HTMLInputType;
use App\Forms\HTMLInputElement;
use App\HTMLElements\HTMLFormElement;
use Exception;

class FormValidator
{
    private HTMLFormElement $form;

    public function __construct(HTMLFormElement $form)
    {
        $this->form = $form;

        $this->validateInputs();
    }

    private function validateInputs()
    {
        $inputs = $this->form->getInputs();
        foreach($inputs as $input)
        {
            $inputType = HTMLInputType::from($input->getType());

            $value = $_POST[$input->getName()] ?? null;

            $this->validateBaseData($input, $value);

            match($inputType) {
                HTMLInputType::DATE => $this->validateDate($input, $value),
                HTMLInputType::DATETIME_LOCAL => $this->validateDatetime($input, $value),
                HTMLInputType::EMAIL => $this->validateEmail($input, $value),
                HTMLInputType::NUMBER => $this->validateNumber($input, $value),
                HTMLInputType::PASSWORD => $this->validatePassword($input, $value),
                HTMLInputType::TEXT => $this->validateText($input, $value),
            };
        }
    }

    private function validateBaseData(HTMLInputElement $input, ?string $value) : void
    {
        $isDisabled = $input->getIsDisabled();
        if($isDisabled && isset($value)) throw new Exception();

        if(!isset($value)) throw new Exception();

        $isRequired = $input->getIsRequired();
        if($isRequired && empty($value)) throw new Exception();
        
        $isReadOnly = $input->getIsReadOnly();
        $defaultValue = $input->getAttribute('value');
        if($isReadOnly && $value !== $defaultValue) throw new Exception();
    }

    private function validateDate(HTMLInputElement $input, string $value) : void
    {
        
        
        
    }

    private function validateDatetime(HTMLInputElement $input, string $value) : void
    {

    }

    private function validateEmail(HTMLInputElement $input, string $value) : void
    {

    }

    private function validateNumber(HTMLInputElement $input, string $value) : void
    {

    }

    private function validatePassword(HTMLInputElement $input, string $value) : void
    {

    }

    private function validateText(HTMLInputElement $input, string $value) : void
    {

    }

    public function getData() : array
    {
        return [];
    }
}