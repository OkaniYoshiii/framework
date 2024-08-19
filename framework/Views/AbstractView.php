<?php

namespace Framework\Views;

use Framework\Forms\HTMLElement;

abstract class AbstractView
{
    abstract public function render() : void;

    abstract public function __toString() : string;
}