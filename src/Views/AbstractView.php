<?php

namespace OkaniYoshiii\Framework\Views;

abstract class AbstractView
{
    abstract public function render() : void;

    abstract public function __toString() : string;
}