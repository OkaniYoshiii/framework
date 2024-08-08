<?php

namespace Framework\Services;


use DateTime;
use Exception;
use Framework\Enums\HTMLInputType;
use Framework\Enums\HttpMethod;
use Framework\Session;
use Framework\Types\Composed\HTMLElements\HTMLFormElement;
use Framework\Types\Composed\HTMLElements\HTMLInputElement;
use Framework\Types\Composed\Request;
use Framework\Types\Strings\Email;
use Framework\Types\Strings\Password;
use Throwable;

class FormValidation
{
    private HTMLFormElement $form;
    private array $validatedData = [];
    private array $errors = [];
    private bool $isCsrfTokenValidated = false;

    public function __construct(HTMLFormElement $form)
    {
        $this->form = $form;

        if(!$this->isSubmitted()) return;

        $this->validate();
    }

    public function isSubmitted() : bool
    {
        if(Request::getInstance()->getMethod() !== HttpMethod::POST->name) return false;

        $inputs = $this->form->getInputs();

        foreach($inputs as $input) 
        {
            $name = $input->getName();

            if(!isset($_POST[$name])) return false;
        }

        return true;
    }

    public function isSuccessful() : bool
    {
        if(!$this->isSubmitted()) return false;

        return empty($this->errors);
    }

    private function validate() : void
    {
        $inputs = $this->form->getInputs();

        foreach($inputs as $input)
        {
            $inputType = HTMLInputType::from($input->getType());

            $value = $_POST[$input->getName()] ?? null;

            if($input->getName() === 'csrf_token') {
                $this->validateCsrfToken($value);
            }

            if($inputType === HTMLInputType::SUBMIT) continue;
            if($inputType === HTMLInputType::HIDDEN) continue;

            $this->validateBaseData($input, $value);

            $this->validatedData[] = match($inputType) {
                HTMLInputType::DATE => $this->getValidatedDate($value),
                HTMLInputType::DATETIME_LOCAL => $this->getValidatedDatetime($value),
                HTMLInputType::EMAIL => $this->getValidatedEmail($value),
                HTMLInputType::NUMBER => (is_numeric($value)) ? $value : null,
                HTMLInputType::PASSWORD => $this->getValidatedPassword($value),
                HTMLInputType::TEXT => is_string($value) ? $value : null,
            };
        }

        if(!$this->isCsrfTokenValidated) throw new Exception('Invalid CSRF token');
    }

    private function validateBaseData(HTMLInputElement $input, ?string $value) : void
    {
        $attributes = $input->getAttributes();
        
        $isDisabled = ($attributes->isset('disabled'));
        if($isDisabled && isset($value)) throw new Exception($input . ' cannot have a value if it is disabled');

        if(!isset($value)) throw new Exception();

        $isRequired = ($attributes->isset('required'));
        if($isRequired && empty($value)) throw new Exception($input . ' cannot be empty if it is required');
        
        $isReadOnly = ($attributes->isset('readonly'));
        $defaultValue = ($attributes->isset('value')) ? $input->getAttribute('value') : '';
        if($isReadOnly && $value !== $defaultValue) throw new Exception();
    }

    private function validateCsrfToken(mixed $value) : void
    {
        $session = Session::getInstance();

        echo $session->get('csrf_token');
        echo '<br>';
        echo $value;

        if($session->get('csrf_token') !== $value) throw new Exception('Invalid CSRF token');

        $this->isCsrfTokenValidated = true;
    }

    private function getValidatedDate(string $value) : DateTime|null
    {
        $date = DateTime::createFromFormat('Y-m-d', $value);
        if(is_null($date)) $this->errors[] = 'La date est incorrecte';
        return ($date) ? $date : null;
    }

    private function getValidatedDatetime(string $value) : DateTime|null
    {
        $date = DateTime::createFromFormat('Y-m-d H:i:s', $value);
        if(is_null($date)) $this->errors[] = 'La date est incorrecte';
        return ($date) ? $date : null;
    }

    private function getValidatedEmail(string $value) : Email|null
    {
        try {
            return new Email($value);
        } catch(Throwable $e) {
            $this->errors[] = 'L\'email est incorrect';
            return null;
        }
    }

    private function getValidatedPassword(string $value) : Password|null
    {
        try {
            return new Password($value);
        } catch(Throwable $e) {
            $this->errors[] = 'Le mot de passe est incorrect';
            return null;
        }
    }

    public function getValidatedData() : array
    {
        return $this->validatedData;
    }

    public function getErrors() : array
    {
        return $this->errors;
    }
}