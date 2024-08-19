<?php

use Framework\Enums\HTMLInputType;
use Framework\Forms\Form;

return (new Form('contact-form'))
    ->setAttributes(['class' => 'd-block'])
    ->addInput('username', HTMLInputType::TEXT,['class' => 'form-input d-none', 'maxlength' => 8, 'novalidate' => true])
    ->addLabel('username', 'Nom d\'utilisateur')
    ->addInput('password', HTMLInputType::PASSWORD, ['class' => 'form-input', 'minlength' => 8, 'novalidate' => true])
    ->addLabel('password', 'Mot de passe', ['class' => 'visually-hidden'])
    ->setSubmitInput('Confirmer', ['class' => 'form-input', 'minlength' => 8, 'novalidate' => true]);