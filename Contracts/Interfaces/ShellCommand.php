<?php

namespace Framework\Contracts\Interfaces;

interface ShellCommand
{
    public static function execute(array $options) : void;
}