<?php

namespace Framework\Views;

abstract class AbstractView
{
    abstract public function render() : void;

    abstract public function __toString() : string;
}