<?php

namespace OkaniYoshiii\Framework;

use Dotenv\Dotenv;

abstract class App
{
    public const FRAMEWORK_DIR = __DIR__ . '/../';
    public const CACHE_DIR = __DIR__ . '/../cache/';

    abstract public static function loadEnvVariables() : void;
}